@php
    $isEditing = isset($coupon);
    $selectedProductIds = collect(old('products', $isEditing ? $coupon->products->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $currentApplicableType = old('applicable_type', $coupon->applicable_type ?? \App\Models\Coupon::APPLICABLE_ALL_PRODUCTS);
    $currentStatus = old('status', $coupon->status ?? \App\Models\Coupon::STATUS_ACTIVE);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        Please fix the highlighted fields and try again.
    </div>
@endif

@error('coupon')
    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $message }}
    </div>
@enderror

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2 text-primary">confirmation_number</span>
                <div>
                    <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Coupon Details</h2>
                    <p class="text-xs text-slate-400">Name, code, discount type, and description.</p>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <label class="block space-y-1">
                    <span class="coupon-label">Coupon Name *</span>
                    <input type="text" name="coupon_name" value="{{ old('coupon_name', $coupon->coupon_name ?? '') }}" class="coupon-input @error('coupon_name') border-red-300 bg-red-50 @enderror" placeholder="Summer Leather Sale" required>
                    @error('coupon_name') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Coupon Code *</span>
                    <input type="text" name="coupon_code" value="{{ old('coupon_code', $coupon->coupon_code ?? '') }}" class="coupon-input uppercase @error('coupon_code') border-red-300 bg-red-50 @enderror" placeholder="LEATHER10" required>
                    @error('coupon_code') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Discount Type *</span>
                    <select name="discount_type" class="coupon-input @error('discount_type') border-red-300 bg-red-50 @enderror" required>
                        @foreach ($discountTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('discount_type', $coupon->discount_type ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('discount_type') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Discount Value *</span>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value ?? '') }}" class="coupon-input @error('discount_value') border-red-300 bg-red-50 @enderror" min="0.01" step="0.01" placeholder="10" required>
                    @error('discount_value') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1 md:col-span-2">
                    <span class="coupon-label">Description</span>
                    <textarea name="description" class="coupon-input min-h-28 @error('description') border-red-300 bg-red-50 @enderror" placeholder="Short internal description">{{ old('description', $coupon->description ?? '') }}</textarea>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2 text-primary">inventory_2</span>
                <div>
                    <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Applicability</h2>
                    <p class="text-xs text-slate-400">Choose whether this coupon works for all products or specific products.</p>
                </div>
            </div>

            <div class="space-y-5">
                <label class="block space-y-1">
                    <span class="coupon-label">Applicable To *</span>
                    <select name="applicable_type" id="applicable_type" class="coupon-input @error('applicable_type') border-red-300 bg-red-50 @enderror" required>
                        @foreach ($applicableTypes as $value => $label)
                            <option value="{{ $value }}" @selected($currentApplicableType === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('applicable_type') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <div id="product-selector-panel" class="{{ $currentApplicableType === \App\Models\Coupon::APPLICABLE_SELECTED_PRODUCTS ? '' : 'hidden' }} rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Selected Products</p>
                            <p class="text-xs text-slate-400">Only active products are shown here.</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-500">{{ $activeProducts->count() }} active</span>
                    </div>

                    <div class="coupon-product-list @error('products') border-red-300 bg-red-50 @enderror @error('products.*') border-red-300 bg-red-50 @enderror">
                        @forelse ($activeProducts as $product)
                            <label class="coupon-product-option">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedProductIds, true))>
                                <span>
                                    <strong>{{ $product->name }}</strong>
                                    @if ($product->sku)
                                        <small>{{ $product->sku }}</small>
                                    @endif
                                </span>
                            </label>
                        @empty
                            <p class="p-4 text-sm text-slate-500">No active products available.</p>
                        @endforelse
                    </div>
                    <p class="mt-2 text-xs text-slate-400">Select one or more products for this coupon.</p>
                    @error('products') <span class="field-error">{{ $message }}</span> @enderror
                    @error('products.*') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2 text-primary">rule</span>
                <div>
                    <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Rules</h2>
                    <p class="text-xs text-slate-400">Order amount and usage limits.</p>
                </div>
            </div>

            <div class="space-y-5">
                <label class="block space-y-1">
                    <span class="coupon-label">Minimum Order Amount</span>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount ?? '') }}" class="coupon-input @error('minimum_order_amount') border-red-300 bg-red-50 @enderror" min="0" step="0.01" placeholder="0.00">
                    @error('minimum_order_amount') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Maximum Discount Amount</span>
                    <input type="number" name="maximum_discount_amount" value="{{ old('maximum_discount_amount', $coupon->maximum_discount_amount ?? '') }}" class="coupon-input @error('maximum_discount_amount') border-red-300 bg-red-50 @enderror" min="0" step="0.01" placeholder="Optional">
                    @error('maximum_discount_amount') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Total Usage Limit</span>
                    <input type="number" name="total_usage_limit" value="{{ old('total_usage_limit', $coupon->total_usage_limit ?? '') }}" class="coupon-input @error('total_usage_limit') border-red-300 bg-red-50 @enderror" min="1" step="1" placeholder="Unlimited">
                    @error('total_usage_limit') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Per User Usage Limit</span>
                    <input type="number" name="per_user_usage_limit" value="{{ old('per_user_usage_limit', $coupon->per_user_usage_limit ?? '') }}" class="coupon-input @error('per_user_usage_limit') border-red-300 bg-red-50 @enderror" min="1" step="1" placeholder="Unlimited">
                    @error('per_user_usage_limit') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2 text-primary">event</span>
                <div>
                    <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Validity</h2>
                    <p class="text-xs text-slate-400">Schedule and publication status.</p>
                </div>
            </div>

            <div class="space-y-5">
                <label class="block space-y-1">
                    <span class="coupon-label">Start Date *</span>
                    <input type="date" name="start_date" value="{{ old('start_date', isset($coupon) ? $coupon->start_date?->format('Y-m-d') : '') }}" class="coupon-input @error('start_date') border-red-300 bg-red-50 @enderror" required>
                    @error('start_date') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">End Date *</span>
                    <input type="date" name="end_date" value="{{ old('end_date', isset($coupon) ? $coupon->end_date?->format('Y-m-d') : '') }}" class="coupon-input @error('end_date') border-red-300 bg-red-50 @enderror" required>
                    @error('end_date') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="block space-y-1">
                    <span class="coupon-label">Status *</span>
                    <select name="status" class="coupon-input @error('status') border-red-300 bg-red-50 @enderror" required>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>
    </aside>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
        Cancel
    </a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
        <span class="material-symbols-outlined !text-[20px]">save</span>
        {{ $isEditing ? 'Update' : 'Create' }}
    </button>
</div>

<style>
    .coupon-label {
        margin-left: 0.25rem;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgb(148 163 184);
    }

    .coupon-input {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(248 250 252);
        padding: 0.75rem;
        font-size: 0.875rem;
        outline: none;
        transition: all 150ms ease;
    }

    .coupon-input:focus {
        border-color: #5D5CFF;
        background: white;
        box-shadow: 0 0 0 3px rgba(93, 92, 255, 0.15);
    }

    .field-error {
        display: block;
        min-height: 1rem;
        padding-left: 0.25rem;
        font-size: 0.75rem;
        color: rgb(220 38 38);
    }

    .coupon-product-list {
        max-height: 18rem;
        overflow-y: auto;
        border: 1px solid rgb(226 232 240);
        border-radius: 0.75rem;
        background: white;
        padding: 0.5rem;
    }

    .coupon-product-option {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border-radius: 0.65rem;
        padding: 0.75rem;
        cursor: pointer;
        transition: background 150ms ease;
    }

    .coupon-product-option:hover {
        background: rgb(248 250 252);
    }

    .coupon-product-option + .coupon-product-option {
        border-top: 1px solid rgb(241 245 249);
    }

    .coupon-product-option input {
        margin-top: 0.25rem;
        height: 1rem;
        width: 1rem;
        accent-color: #5D5CFF;
    }

    .coupon-product-option strong {
        display: block;
        color: rgb(15 23 42);
        font-size: 0.875rem;
        line-height: 1.35;
    }

    .coupon-product-option small {
        display: block;
        margin-top: 0.15rem;
        color: rgb(148 163 184);
        font-size: 0.75rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const applicableType = document.getElementById('applicable_type');
        const productPanel = document.getElementById('product-selector-panel');

        function syncProductPanel() {
            productPanel.classList.toggle('hidden', applicableType.value !== 'selected_products');
        }

        applicableType.addEventListener('change', syncProductPanel);

        syncProductPanel();
    });
</script>
