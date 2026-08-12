<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $coupons = Coupon::query()
            ->withCount('products')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('coupon_name', 'like', '%'.$search.'%')
                        ->orWhere('coupon_code', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, array_keys($this->statuses()), true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.coupons.index', [
            'coupons' => $coupons,
            'search' => $search,
            'status' => $status,
            'statuses' => $this->statuses(),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request), $this->messages());

        try {
            DB::beginTransaction();

            $coupon = Coupon::create($this->payload($validated));
            $this->syncProducts($coupon, $validated);

            DB::commit();

            return redirect()
                ->route('admin.coupons.index')
                ->with('status', 'Coupon created successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['coupon' => 'Unable to save coupon. Please try again.']);
        }
    }

    public function edit(Coupon $coupon): View
    {
        $coupon->load('products:id');

        return view('admin.coupons.edit', array_merge($this->formData(), [
            'coupon' => $coupon,
        ]));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, $coupon), $this->messages());

        try {
            DB::beginTransaction();

            $coupon->update($this->payload($validated));
            $this->syncProducts($coupon, $validated);

            DB::commit();

            return redirect()
                ->route('admin.coupons.index')
                ->with('status', 'Coupon updated successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['coupon' => 'Unable to update coupon. Please try again.']);
        }
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        try {
            $coupon->delete();

            return redirect()
                ->route('admin.coupons.index')
                ->with('status', 'Coupon deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['coupon' => 'Unable to delete coupon. Please try again.']);
        }
    }

    public function updateStatus(Coupon $coupon): RedirectResponse
    {
        $coupon->update([
            'status' => $coupon->isActive() ? Coupon::STATUS_INACTIVE : Coupon::STATUS_ACTIVE,
        ]);

        return back()->with('status', 'Coupon status updated successfully.');
    }

    private function formData(): array
    {
        return [
            'activeProducts' => Product::query()
                ->where('status', Product::STATUS_ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'sku']),
            'discountTypes' => $this->discountTypes(),
            'applicableTypes' => $this->applicableTypes(),
            'statuses' => $this->statuses(),
        ];
    }

    private function rules(Request $request, ?Coupon $coupon = null): array
    {
        $selectedProducts = $request->input('applicable_type') === Coupon::APPLICABLE_SELECTED_PRODUCTS;

        return [
            'coupon_name' => ['required', 'string', 'max:255'],
            'coupon_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('coupons', 'coupon_code')->ignore($coupon?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'discount_type' => ['required', Rule::in(array_keys($this->discountTypes()))],
            'discount_value' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'applicable_type' => ['required', Rule::in(array_keys($this->applicableTypes()))],
            'products' => array_values(array_filter([$selectedProducts ? 'required' : 'nullable', 'array', $selectedProducts ? 'min:1' : null])),
            'products.*' => ['integer', 'distinct', Rule::exists('products', 'id')->where('status', Product::STATUS_ACTIVE)],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'maximum_discount_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'total_usage_limit' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'per_user_usage_limit' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ];
    }

    private function messages(): array
    {
        return [
            'coupon_code.unique' => 'This coupon code already exists.',
            'discount_value.gt' => 'Discount value must be greater than zero.',
            'products.required' => 'Select at least one active product for selected product coupons.',
            'products.min' => 'Select at least one active product for selected product coupons.',
            'products.*.distinct' => 'Duplicate products are not allowed.',
            'start_date.before_or_equal' => 'Start date cannot be after end date.',
            'end_date.after_or_equal' => 'End date cannot be before start date.',
        ];
    }

    private function payload(array $validated): array
    {
        return [
            'coupon_name' => $validated['coupon_name'],
            'coupon_code' => Str::upper($validated['coupon_code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'applicable_type' => $validated['applicable_type'],
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'total_usage_limit' => $validated['total_usage_limit'] ?? null,
            'per_user_usage_limit' => $validated['per_user_usage_limit'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ];
    }

    private function syncProducts(Coupon $coupon, array $validated): void
    {
        if ($validated['applicable_type'] !== Coupon::APPLICABLE_SELECTED_PRODUCTS) {
            $coupon->products()->sync([]);

            return;
        }

        $coupon->products()->sync(array_values(array_unique($validated['products'] ?? [])));
    }

    private function discountTypes(): array
    {
        return [
            Coupon::DISCOUNT_PERCENTAGE => 'Percentage',
            Coupon::DISCOUNT_FIXED => 'Fixed Amount',
        ];
    }

    private function applicableTypes(): array
    {
        return [
            Coupon::APPLICABLE_ALL_PRODUCTS => 'All Products',
            Coupon::APPLICABLE_SELECTED_PRODUCTS => 'Selected Products',
        ];
    }

    private function statuses(): array
    {
        return [
            Coupon::STATUS_ACTIVE => 'Active',
            Coupon::STATUS_INACTIVE => 'Inactive',
        ];
    }
}
