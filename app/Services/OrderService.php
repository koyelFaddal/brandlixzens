<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\DeliverySetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly CouponService $coupons,
    )
    {
    }

    public function createFromCart(User $user, array $deliveryAddress, string $paymentMethod, ?string $couponCode = null): Order
    {
        return DB::transaction(function () use ($user, $deliveryAddress, $paymentMethod, $couponCode): Order {
            $cartItems = CartItem::query()
                ->with(['product.primaryImage'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                abort(422, 'Your cart is empty.');
            }

            $lockedProducts = $this->lockProductsForOrder(
                $cartItems->pluck('product_id')->unique()->values()->all(),
            );

            $order = $this->createFromProducts(
                $user,
                $deliveryAddress,
                $paymentMethod,
                $cartItems
                    ->groupBy('product_id')
                    ->map(fn ($items, int $productId): array => [
                        'product' => $lockedProducts->get($productId),
                        'quantity' => (int) $items->sum('quantity'),
                    ])
                    ->values()
                    ->all(),
                $couponCode,
            );

            CartItem::query()->where('user_id', $user->id)->delete();

            return $order->load('items');
        }, 3);
    }

    public function createBuyNow(User $user, Product $product, int $quantity, array $deliveryAddress, string $paymentMethod, ?string $couponCode = null): Order
    {
        return DB::transaction(function () use ($user, $product, $quantity, $deliveryAddress, $paymentMethod, $couponCode): Order {
            $lockedProduct = $this->lockProductsForOrder([$product->id])->first();

            $order = $this->createFromProducts($user, $deliveryAddress, $paymentMethod, [[
                'product' => $lockedProduct,
                'quantity' => max(1, $quantity),
            ]], $couponCode);

            return $order->load('items');
        }, 3);
    }

    private function createFromProducts(User $user, array $deliveryAddress, string $paymentMethod, array $items, ?string $couponCode = null): Order
    {
        foreach ($items as $item) {
            $product = $item['product'];
            $quantity = max(1, (int) $item['quantity']);

            abort_if(! $product || $product->status !== Product::STATUS_ACTIVE, 422, 'One or more products are no longer available.');
            $product->ensureAvailableQuantity($quantity);
        }

        $subtotal = collect($items)->sum(fn (array $item): float => $item['product']->priceForUser($user) * (int) $item['quantity']);
        $couponResult = null;
        $couponDiscount = 0.0;

        if ($couponCode) {
            $couponResult = $this->coupons->preview($user, $couponCode, $items);
            $couponDiscount = (float) $couponResult['discount_amount'];
        }

        $deliverySetting = DeliverySetting::current();
        $discountedSubtotal = max(0, $subtotal - $couponDiscount);
        $deliveryCharge = $deliverySetting->chargeFor($discountedSubtotal);
        $grandTotal = $discountedSubtotal + $deliveryCharge;
        $coupon = $couponResult['coupon'] ?? null;

        $order = Order::query()->create([
            'order_number' => $this->generateOrderNumber(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'delivery_name' => $deliveryAddress['name'],
            'delivery_phone' => $deliveryAddress['phone'],
            'delivery_email' => $deliveryAddress['email'] ?? $user->email,
            'address_line_1' => $deliveryAddress['address_line_1'],
            'address_line_2' => $deliveryAddress['address_line_2'] ?? null,
            'city' => $deliveryAddress['city'],
            'state' => $deliveryAddress['state'],
            'postal_code' => $deliveryAddress['postal_code'],
            'country' => $deliveryAddress['country'] ?? 'India',
            'delivery_notes' => $deliveryAddress['notes'] ?? null,
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'coupon_id' => $coupon?->id,
            'coupon_code' => $coupon?->coupon_code,
            'coupon_name' => $coupon?->coupon_name,
            'coupon_discount_amount' => $couponDiscount,
            'grand_total' => $grandTotal,
            'payment_method' => $paymentMethod,
            'status' => Order::STATUS_PENDING,
            'ordered_at' => now(),
        ]);

        foreach ($items as $item) {
            $product = $item['product'];
            $quantity = (int) $item['quantity'];
            $unitPrice = $product->priceForUser($user);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'line_total' => $unitPrice * $quantity,
                'product_image' => $product->primaryImage?->image,
            ]);

            $product->decreaseStock($quantity);
        }

        return $order;
    }

    public function updateStatus(Order $order, string $status, array $attributes = []): Order
    {
        return DB::transaction(function () use ($order, $status, $attributes): Order {
            $lockedOrder = Order::query()
                ->with('items')
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();
            $previousStatus = $lockedOrder->status;

            $lockedOrder->update(array_merge($attributes, ['status' => $status]));

            if ($this->shouldRestoreStock($previousStatus, $status)) {
                $this->restoreStock($lockedOrder);
            }

            return $lockedOrder->refresh()->load('items');
        }, 3);
    }

    private function shouldRestoreStock(string $previousStatus, string $nextStatus): bool
    {
        return ! in_array($previousStatus, [Order::STATUS_CANCELLED, Order::STATUS_REJECTED], true)
            && in_array($nextStatus, [Order::STATUS_CANCELLED, Order::STATUS_REJECTED], true);
    }

    private function restoreStock(Order $order): void
    {
        $productIds = $order->items
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $products = $this->lockProductsForOrder($productIds);

        $order->items
            ->groupBy('product_id')
            ->each(function ($items, int $productId) use ($products): void {
                $product = $products->get($productId);

                if ($product) {
                    $product->increaseStock((int) $items->sum('quantity'));
                }
            });
    }

    private function lockProductsForOrder(array $productIds): EloquentCollection
    {
        $productIds = collect($productIds)
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Product::query()
            ->with('primaryImage')
            ->whereKey($productIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'FL-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
