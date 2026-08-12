<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function availableCoupons(): Collection
    {
        return Coupon::query()
            ->where('status', Coupon::STATUS_ACTIVE)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    public function preview(?User $user, string $code, array $items): array
    {
        if ($user?->isApprovedDealer()) {
            $this->invalid('Coupons are not available for dealer accounts.');
        }

        $coupon = Coupon::query()
            ->with('products:id')
            ->where('coupon_code', Str::upper(trim($code)))
            ->first();

        if (! $coupon) {
            $this->invalid('This coupon is not valid for your cart.');
        }

        return $this->calculate($coupon, $user, $items);
    }

    public function calculate(Coupon $coupon, ?User $user, array $items): array
    {
        if ($user?->isApprovedDealer()) {
            $this->invalid('Coupons are not available for dealer accounts.');
        }

        if ($coupon->status !== Coupon::STATUS_ACTIVE) {
            $this->invalid('This coupon is not active.');
        }

        $today = now()->toDateString();

        if ($coupon->start_date->toDateString() > $today || $coupon->end_date->toDateString() < $today) {
            $this->invalid('This coupon is not valid today.');
        }

        $normalizedItems = collect($items)
            ->map(function (array $item) use ($user): array {
                $product = $item['product'] ?? null;
                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => $product instanceof Product ? $product->priceForUser($user) * $quantity : 0,
                ];
            })
            ->filter(fn (array $item): bool => $item['product'] instanceof Product)
            ->values();

        if ($normalizedItems->isEmpty()) {
            $this->invalid('Your cart is empty.');
        }

        $subtotal = (float) $normalizedItems->sum('line_total');

        if ($coupon->minimum_order_amount !== null && $subtotal < (float) $coupon->minimum_order_amount) {
            $this->invalid('Minimum order amount not reached for this coupon.');
        }

        $eligibleSubtotal = $subtotal;

        if ($coupon->applicable_type === Coupon::APPLICABLE_SELECTED_PRODUCTS) {
            $selectedProductIds = $coupon->products->pluck('id')->map(fn ($id): int => (int) $id)->all();
            $eligibleSubtotal = (float) $normalizedItems
                ->filter(fn (array $item): bool => in_array((int) $item['product']->id, $selectedProductIds, true))
                ->sum('line_total');

            if ($eligibleSubtotal <= 0) {
                $this->invalid('This coupon is not valid for your selected products.');
            }
        }

        $this->checkUsageLimits($coupon, $user);

        $discount = $coupon->discount_type === Coupon::DISCOUNT_PERCENTAGE
            ? $eligibleSubtotal * ((float) $coupon->discount_value / 100)
            : (float) $coupon->discount_value;

        if ($coupon->maximum_discount_amount !== null && (float) $coupon->maximum_discount_amount > 0) {
            $discount = min($discount, (float) $coupon->maximum_discount_amount);
        }

        $discount = round(min($discount, $subtotal), 2);

        if ($discount <= 0) {
            $this->invalid('This coupon does not add a discount to your cart.');
        }

        return [
            'coupon' => $coupon,
            'discount_amount' => $discount,
            'subtotal' => round($subtotal, 2),
            'eligible_subtotal' => round($eligibleSubtotal, 2),
        ];
    }

    private function checkUsageLimits(Coupon $coupon, ?User $user): void
    {
        $orders = Order::query()
            ->where('coupon_id', $coupon->id)
            ->whereNotIn('status', [Order::STATUS_CANCELLED, Order::STATUS_REJECTED]);

        if ($coupon->total_usage_limit !== null && (clone $orders)->count() >= (int) $coupon->total_usage_limit) {
            $this->invalid('This coupon usage limit has been reached.');
        }

        if ($user && $coupon->per_user_usage_limit !== null) {
            $usedByUser = (clone $orders)->where('user_id', $user->id)->count();

            if ($usedByUser >= (int) $coupon->per_user_usage_limit) {
                $this->invalid('You have already used this coupon.');
            }
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['coupon_code' => $message]);
    }
}
