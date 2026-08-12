@php
    $isEditing = isset($category);
    $isActive = old('is_active', $isEditing ? (int) $category->is_active : 1);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        Please fix the highlighted fields and try again.
    </div>
@endif

<div class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
    <label class="block space-y-1">
        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category Name *</span>
        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            class="category-input @error('name') border-red-300 bg-red-50 @enderror"
            placeholder="Belts"
            required>
        @error('name')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="block space-y-1">
        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</span>
        <textarea
            name="description"
            class="category-input min-h-32 @error('description') border-red-300 bg-red-50 @enderror"
            placeholder="Short category description">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="block space-y-1">
        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">
            Image{{ $isEditing ? '' : ' *' }}
        </span>
        <input
            type="file"
            name="image"
            accept="image/jpeg,image/jpg,image/png,image/webp"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-primary hover:file:bg-primary/20 @error('image') border-red-300 bg-red-50 @enderror"
            {{ $isEditing ? '' : 'required' }}>
        <span class="block px-1 text-xs text-slate-400">Max 2MB. Accepted: JPG, PNG, WebP.</span>
        @error('image')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-900">Active/Inactive</p>
        <label class="toggle-switch">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ (int) $isActive === 1 ? 'checked' : '' }}>
            <span></span>
        </label>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
        Cancel
    </a>
    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
        <span class="material-symbols-outlined !text-[20px]">save</span>
        {{ $isEditing ? 'Update' : 'Create' }}
    </button>
</div>

<style>
    .category-input {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(248 250 252);
        padding: 0.75rem;
        font-size: 0.875rem;
        outline: none;
        transition: all 150ms ease;
    }

    .category-input:focus {
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

    .toggle-switch {
        position: relative;
        display: inline-flex;
        height: 1.75rem;
        width: 3.25rem;
        flex-shrink: 0;
        cursor: pointer;
        align-items: center;
    }

    .toggle-switch input[type="checkbox"] {
        position: absolute;
        opacity: 0;
    }

    .toggle-switch span {
        position: relative;
        height: 1.75rem;
        width: 3.25rem;
        border-radius: 9999px;
        background: rgb(203 213 225);
        transition: background 150ms ease;
    }

    .toggle-switch span::after {
        position: absolute;
        left: 0.25rem;
        top: 0.25rem;
        height: 1.25rem;
        width: 1.25rem;
        border-radius: 9999px;
        background: white;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.2);
        content: "";
        transition: transform 150ms ease;
    }

    .toggle-switch input[type="checkbox"]:checked + span {
        background: #5D5CFF;
    }

    .toggle-switch input[type="checkbox"]:checked + span::after {
        transform: translateX(1.5rem);
    }
</style>
