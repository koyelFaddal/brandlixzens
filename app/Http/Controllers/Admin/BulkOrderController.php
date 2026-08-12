<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BulkOrderApproved;
use App\Mail\BulkOrderRejected;
use App\Models\BulkOrder;
use App\Services\ErrorLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class BulkOrderController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $bulkOrders = BulkOrder::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('product_name', 'like', $search.'%')
                        ->orWhere('full_name', 'like', $search.'%')
                        ->orWhere('email', 'like', $search.'%')
                        ->orWhere('phone', 'like', $search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.bulk-orders.index', [
            'bulkOrders' => $bulkOrders,
            'search' => $search,
        ]);
    }

    public function show(BulkOrder $bulkOrder): View
    {
        return view('admin.bulk-orders.show', [
            'bulkOrder' => $bulkOrder,
        ]);
    }

    public function approve(BulkOrder $bulkOrder): RedirectResponse
    {
        if (! $bulkOrder->canApprove()) {
            return back()->withErrors([
                'bulk_order' => "This bulk order is already {$bulkOrder->status_label} and cannot be approved.",
            ]);
        }

        try {
            $bulkOrder->update([
                'status' => BulkOrder::STATUS_APPROVED,
                'reject_reason' => null,
            ]);

            Mail::to($bulkOrder->email)->send(new BulkOrderApproved($bulkOrder));

            return back()->with('status', 'Bulk order approved successfully.');
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin bulk order approve failed');

            return back()->withErrors(['bulk_order' => 'Unable to approve bulk order. Please try again.']);
        }
    }

    public function reject(Request $request, BulkOrder $bulkOrder): RedirectResponse
    {
        if (! $bulkOrder->canReject()) {
            return back()->withErrors([
                'bulk_order' => "This bulk order is already {$bulkOrder->status_label} and cannot be rejected.",
            ]);
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $bulkOrder->update([
                'status' => BulkOrder::STATUS_REJECTED,
                'reject_reason' => $validated['reject_reason'],
            ]);

            Mail::to($bulkOrder->email)->send(new BulkOrderRejected($bulkOrder));

            return back()->with('status', 'Bulk order rejected successfully.');
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin bulk order reject failed');

            return back()->withErrors(['bulk_order' => 'Unable to reject bulk order. Please try again.']);
        }
    }

    public function destroy(BulkOrder $bulkOrder): RedirectResponse
    {
        try {
            $bulkOrder->delete();

            return redirect()
                ->route('admin.bulk-orders.index')
                ->with('status', 'Bulk order deleted successfully.');
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin bulk order delete failed');

            return back()->withErrors(['bulk_order' => 'Unable to delete bulk order. Please try again.']);
        }
    }
}
