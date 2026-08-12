<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerApiToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $customers = User::query()
            ->where('is_admin', false)
            ->where(function ($query): void {
                $query->whereNull('user_type')
                    ->orWhere('user_type', 'customer');
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function show(User $customer): View
    {
        $this->ensureCustomer($customer);

        return view('admin.customers.show', [
            'customer' => $customer,
        ]);
    }

    public function updateStatus(Request $request, User $customer): RedirectResponse
    {
        $this->ensureCustomer($customer);

        $validated = $request->validate([
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $isActive = $validated['is_active'] === '1';
        $customer->update(['is_active' => $isActive]);

        if (! $isActive) {
            $this->invalidateCustomerSessions($customer);
        }

        return back()->with('status', 'Customer status updated successfully.');
    }

    public function destroy(User $customer): RedirectResponse
    {
        $this->ensureCustomer($customer);
        $this->invalidateCustomerSessions($customer);
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('status', 'Customer deleted successfully.');
    }

    private function ensureCustomer(User $customer): void
    {
        abort_if(
            $customer->is_admin || ($customer->user_type !== null && $customer->user_type !== 'customer'),
            404
        );
    }

    private function invalidateCustomerSessions(User $customer): void
    {
        CustomerApiToken::query()
            ->where('user_id', $customer->id)
            ->delete();
    }
}
