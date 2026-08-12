<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderRejected;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $orders = Order::query()
            ->with(['user:id,name,email', 'giftOrder'])
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('customer_email', 'like', '%'.$search.'%')
                        ->orWhere('customer_phone', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== '' && array_key_exists($status, Order::statuses()), fn ($query) => $query->where('status', $status))
            ->latest('ordered_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'statuses' => Order::statuses(),
            'paymentMethods' => Order::paymentMethods(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'user:id,name,email,phone', 'giftOrder']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'nextActions' => Order::workflowActionsFor($order->status),
            'paymentMethods' => Order::paymentMethods(),
            'isGiftOrderContext' => false,
        ]);
    }

    public function updateStatus(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $nextActions = Order::workflowActionsFor($order->status);

        if ($nextActions === []) {
            return back()->withErrors([
                'status' => 'This order has reached a final status and cannot be updated.',
            ]);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys($nextActions))],
            'rejection_reason' => [
                Rule::requiredIf(fn (): bool => $request->input('status') === Order::STATUS_REJECTED),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === Order::STATUS_REJECTED) {
            $updates['rejection_reason'] = trim((string) $validated['rejection_reason']);
        }

        $order = $orders->updateStatus($order, $validated['status'], $updates);

        if ($order->status === Order::STATUS_REJECTED) {
            Mail::to($order->customer_email)->send(new OrderRejected($order));
        } else {
            Mail::to($order->customer_email)->send(new OrderStatusUpdated($order));
        }

        return back()->with('status', 'Order status updated successfully.');
    }

    public function updateDeliverySchedule(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'expected_delivery_date' => ['nullable', 'date'],
            'expected_delivery_time' => ['nullable', 'date_format:H:i'],
        ]);

        $order->update([
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'expected_delivery_time' => $validated['expected_delivery_time'] ?? null,
        ]);

        return back()->with('status', 'Delivery schedule updated successfully.');
    }
}
