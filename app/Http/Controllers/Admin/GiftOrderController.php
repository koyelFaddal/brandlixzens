<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\GreetingCardRenderer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GiftOrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $orders = Order::query()
            ->with(['user:id,name,email', 'giftOrder'])
            ->withCount('items')
            ->whereHas('giftOrder')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('customer_email', 'like', '%'.$search.'%')
                        ->orWhereHas('giftOrder', function ($query) use ($search): void {
                            $query->where('greeting_card_title', 'like', '%'.$search.'%')
                                ->orWhere('greeting_message', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($status !== '' && array_key_exists($status, Order::statuses()), fn ($query) => $query->where('status', $status))
            ->latest('ordered_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gift-orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'statuses' => Order::statuses(),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->giftOrder()->exists(), 404);

        $order->load(['items', 'user:id,name,email,phone', 'giftOrder']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'nextActions' => Order::workflowActionsFor($order->status),
            'paymentMethods' => Order::paymentMethods(),
            'isGiftOrderContext' => true,
        ]);
    }

    public function greetingCard(Request $request, Order $order): Response
    {
        $order->load('giftOrder');
        abort_unless($order->giftOrder && $order->giftOrder->include_greeting_card, 404);

        $filename = $order->order_number.'-greeting-card.html';

        return response(GreetingCardRenderer::renderHtml($order->giftOrder), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
