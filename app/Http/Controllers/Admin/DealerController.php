<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerApiToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DealerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $dealers = User::query()
            ->where('is_admin', false)
            ->where('user_type', 'dealer')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('gst_number', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.dealers.index', [
            'dealers' => $dealers,
            'search' => $search,
            'dealerStatuses' => User::dealerStatuses(),
        ]);
    }

    public function show(User $dealer): View
    {
        $this->ensureDealer($dealer);

        return view('admin.dealers.show', [
            'dealer' => $dealer,
            'dealerStatuses' => User::dealerStatuses(),
        ]);
    }

    public function destroy(User $dealer): RedirectResponse
    {
        $this->ensureDealer($dealer);
        $this->invalidateDealerSessions($dealer);
        $dealer->delete();

        return redirect()
            ->route('admin.dealers.index')
            ->with('status', 'Dealer deleted successfully.');
    }

    public function updateStatus(Request $request, User $dealer): RedirectResponse
    {
        $this->ensureDealer($dealer);

        $validated = $request->validate([
            'dealer_status' => ['nullable', Rule::in(array_keys(User::dealerStatuses()))],
            'is_active' => ['nullable', Rule::in(['0', '1'])],
        ]);

        $updates = [];

        if (array_key_exists('dealer_status', $validated) && $validated['dealer_status'] !== null) {
            $updates['dealer_status'] = $validated['dealer_status'];
        }

        if (array_key_exists('is_active', $validated) && $validated['is_active'] !== null) {
            $isActive = $validated['is_active'] === '1';
            $updates['is_active'] = $isActive;

            if (! $isActive) {
                $this->invalidateDealerSessions($dealer);
            }
        }

        if ($updates === []) {
            return back()->withErrors(['status' => 'Please choose a dealer status to update.']);
        }

        $dealer->update($updates);

        return back()->with('status', 'Dealer status updated successfully.');
    }

    private function ensureDealer(User $dealer): void
    {
        abort_if($dealer->is_admin || $dealer->user_type !== 'dealer', 404);
    }

    private function invalidateDealerSessions(User $dealer): void
    {
        CustomerApiToken::query()
            ->where('user_id', $dealer->id)
            ->delete();
    }
}
