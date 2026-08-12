@php
    $isEditing = isset($product);
    $selectedCategories = collect(old('categories', $isEditing ? $product->categories->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $isRecommended = old('is_recommended', $isEditing ? (int) $product->is_recommended : 0);
    $descriptionValue = old('description', $product->description ?? '');
    $advantagesValue = old('advantages', $product->advantages ?? '');
    $materialNotesValue = old('material_notes', $product->material_notes ?? '');
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        Please fix the highlighted fields and try again.
    </div>
@endif

@error('product')
    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $message }}
    </div>
@enderror

<div class="product-form-layout">
    <div class="product-form-stack">
        <section class="product-panel">
            <div class="panel-heading">
                <span class="material-symbols-outlined">inventory_2</span>
                <div>
                    <h2>Basic Information</h2>
                    <p>Name, SKU, category, and summary details.</p>
                </div>
            </div>

            <div class="sidebar-field-list">
                <label class="block space-y-1">
                    <span class="form-label">Product Name *</span>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="product-input @error('name') border-red-300 bg-red-50 @enderror" placeholder="Heritage Brass Belt" required>
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <div class="grid grid-cols-1 gap-4">
                    <label class="block space-y-1">
                        <span class="form-label">Product SKU</span>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="product-input @error('sku') border-red-300 bg-red-50 @enderror" placeholder="Leave blank to auto-generate">
                        <span class="helper-text">Auto-generated if left empty.</span>
                        @error('sku')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <!-- <label class="block space-y-1">
                        <span class="form-label">Expiry Date</span>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', isset($product) && $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '') }}" class="product-input @error('expiry_date') border-red-300 bg-red-50 @enderror">
                        @error('expiry_date')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label> -->
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="form-label">Category *</span>
                        <p class="helper-text">Select one or more active categories.</p>
                    </div>

                    <div class="category-picker @error('categories') border-red-300 bg-red-50 @enderror">
                        <label class="category-option category-select-all">
                            <input type="checkbox" id="category-select-all">
                            <span>Select All</span>
                        </label>
                        @foreach ($categories as $category)
                            <label class="category-option" data-category-option>
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedCategories, true) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('categories')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                    @error('categories.*')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <label class="block space-y-1">
                    <span class="form-label">Short Description</span>
                    <textarea name="short_description" class="product-input min-h-24 @error('short_description') border-red-300 bg-red-50 @enderror" placeholder="Brief summary shown in product listings">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    @error('short_description')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        <section class="product-panel">
            <div class="panel-heading">
                <span class="material-symbols-outlined">imagesmode</span>
                <div>
                    <h2>Product Images</h2>
                    <p>Upload one or more product photos. New selected images preview below.</p>
                </div>
            </div>

            <label class="image-dropzone" for="product-images-input">
                <span class="material-symbols-outlined upload-icon">cloud_upload</span>
                <span class="dropzone-title">Drag and drop images here or</span>
                <span class="browse-button">
                    <span class="material-symbols-outlined !text-[20px]">folder_open</span>
                    Browse Files
                </span>
                <input id="product-images-input" type="file" name="images[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="sr-only">
            </label>
            <p class="helper-text mt-2">Recommended Image Size: <strong>512 x 280 px</strong>. Multiple images supported. Max 2MB each. Accepted: JPG, PNG, WebP.</p>

            <div id="selected-image-preview" class="image-preview-grid hidden"></div>

            @if ($isEditing && $product->images->isNotEmpty())
                <div class="mt-6">
                    <p class="form-label mb-3">Uploaded Images</p>
                    <div class="image-preview-grid">
                        @foreach ($product->images as $image)
                            <label class="image-card">
                                <img src="{{ asset('storage/'.$image->image) }}" alt="{{ $product->name }}">
                                <span class="existing-remove-button">
                                    <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                    <span class="material-symbols-outlined !text-[18px]">close</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @error('images')
                <span class="field-error">{{ $message }}</span>
            @enderror
            @error('images.*')
                <span class="field-error">{{ $message }}</span>
            @enderror
            @error('remove_images.*')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </section>

        <section class="product-panel">
            <div class="panel-heading">
                <span class="material-symbols-outlined">description</span>
                <div>
                    <h2>Product Description</h2>
                    <p>Rich content and frontend detail notes.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="space-y-1">
                    <span class="form-label">Description</span>
                    <div class="editor-toolbar">
                        <button type="button" class="editor-button" data-editor-command="bold"><span class="material-symbols-outlined !text-[20px]">format_bold</span></button>
                        <button type="button" class="editor-button" data-editor-command="italic"><span class="material-symbols-outlined !text-[20px]">format_italic</span></button>
                        <button type="button" class="editor-button" data-editor-command="underline"><span class="material-symbols-outlined !text-[20px]">format_underlined</span></button>
                        <button type="button" class="editor-button" data-editor-command="insertUnorderedList"><span class="material-symbols-outlined !text-[20px]">format_list_bulleted</span></button>
                        <button type="button" class="editor-button" data-editor-command="insertOrderedList"><span class="material-symbols-outlined !text-[20px]">format_list_numbered</span></button>
                    </div>
                    <div id="description-editor" class="rich-editor" contenteditable="true">{!! $descriptionValue !!}</div>
                    <textarea name="description" id="description-input" class="hidden">{{ $descriptionValue }}</textarea>
                    @error('description')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <label class="block space-y-1">
                        <span class="form-label">Material</span>
                        <input type="text" name="material" value="{{ old('material', $product->material ?? '') }}" class="product-input @error('material') border-red-300 bg-red-50 @enderror" placeholder="Full Grain Leather">
                        @error('material')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block space-y-1">
                        <span class="form-label">Color</span>
                        <input type="text" name="color" value="{{ old('color', $product->color ?? '') }}" class="product-input @error('color') border-red-300 bg-red-50 @enderror" placeholder="Mahogany Brown">
                        @error('color')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block space-y-1">
                        <span class="form-label">Size</span>
                        <input type="text" name="size" value="{{ old('size', $product->size ?? '') }}" class="product-input @error('size') border-red-300 bg-red-50 @enderror" placeholder="32-42 in">
                        @error('size')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="space-y-1">
                        <span class="form-label">Advantages</span>
                        <div class="editor-toolbar">
                            <button type="button" class="editor-button" data-editor-command="bold" data-editor-target="advantages-editor"><span class="material-symbols-outlined !text-[20px]">format_bold</span></button>
                            <button type="button" class="editor-button" data-editor-command="italic" data-editor-target="advantages-editor"><span class="material-symbols-outlined !text-[20px]">format_italic</span></button>
                            <button type="button" class="editor-button" data-editor-command="underline" data-editor-target="advantages-editor"><span class="material-symbols-outlined !text-[20px]">format_underlined</span></button>
                            <button type="button" class="editor-button" data-editor-command="insertUnorderedList" data-editor-target="advantages-editor"><span class="material-symbols-outlined !text-[20px]">format_list_bulleted</span></button>
                            <button type="button" class="editor-button" data-editor-command="insertOrderedList" data-editor-target="advantages-editor"><span class="material-symbols-outlined !text-[20px]">format_list_numbered</span></button>
                        </div>
                        <div id="advantages-editor" class="rich-editor rich-editor-small" contenteditable="true">{!! $advantagesValue !!}</div>
                        <textarea name="advantages" id="advantages-input" class="hidden">{{ $advantagesValue }}</textarea>
                        @error('advantages')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <span class="form-label">Material Notes</span>
                        <div class="editor-toolbar">
                            <button type="button" class="editor-button" data-editor-command="bold" data-editor-target="material-notes-editor"><span class="material-symbols-outlined !text-[20px]">format_bold</span></button>
                            <button type="button" class="editor-button" data-editor-command="italic" data-editor-target="material-notes-editor"><span class="material-symbols-outlined !text-[20px]">format_italic</span></button>
                            <button type="button" class="editor-button" data-editor-command="underline" data-editor-target="material-notes-editor"><span class="material-symbols-outlined !text-[20px]">format_underlined</span></button>
                            <button type="button" class="editor-button" data-editor-command="insertUnorderedList" data-editor-target="material-notes-editor"><span class="material-symbols-outlined !text-[20px]">format_list_bulleted</span></button>
                            <button type="button" class="editor-button" data-editor-command="insertOrderedList" data-editor-target="material-notes-editor"><span class="material-symbols-outlined !text-[20px]">format_list_numbered</span></button>
                        </div>
                        <div id="material-notes-editor" class="rich-editor rich-editor-small" contenteditable="true">{!! $materialNotesValue !!}</div>
                        <textarea name="material_notes" id="material-notes-input" class="hidden">{{ $materialNotesValue }}</textarea>
                        @error('material_notes')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </section>
    </div>

    <aside class="product-form-stack product-form-sidebar">
        <section class="product-panel">
            <div class="panel-heading">
                <span class="material-symbols-outlined">publish</span>
                <div>
                    <h2>Publish</h2>
                    <p>Status and recommendation settings.</p>
                </div>
            </div>

            <div class="space-y-5">
                <label class="side-field-card">
                    <span class="form-label">Status *</span>
                    <select name="status" class="product-input @error('status') border-red-300 bg-red-50 @enderror" required>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $product->status ?? 'draft') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="helper-text">New products default to draft status.</span>
                    @error('status')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <div class="setting-row side-field-card">
                    <span>Recommended Product</span>
                    <label class="toggle-switch">
                        <input type="hidden" name="is_recommended" value="0">
                        <input type="checkbox" name="is_recommended" value="1" {{ (int) $isRecommended === 1 ? 'checked' : '' }}>
                        <span></span>
                    </label>
                </div>
            </div>
        </section>

        <section class="product-panel">
            <div class="panel-heading">
                <span class="material-symbols-outlined">payments</span>
                <div>
                    <h2>Pricing</h2>
                    <p>Price and stock availability.</p>
                </div>
            </div>

            <div class="sidebar-field-list">
                <label class="side-field-card">
                    <span class="form-label">Product Price *</span>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" step="0.01" class="product-input @error('price') border-red-300 bg-red-50 @enderror" placeholder="0.00" required>
                    @error('price')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="side-field-card">
                    <span class="form-label">Discounted Price</span>
                    <input type="number" name="discounted_price" value="{{ old('discounted_price', $product->discounted_price ?? '') }}" min="0" step="0.01" class="product-input @error('discounted_price') border-red-300 bg-red-50 @enderror" placeholder="0.00">
                    <span class="helper-text">Leave blank or set 0 to use the normal price.</span>
                    @error('discounted_price')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="side-field-card">
                    <span class="form-label">Dealer Price *</span>
                    <input type="number" name="dealer_price" value="{{ old('dealer_price', $product->dealer_price ?? 0) }}" min="0" step="0.01" class="product-input @error('dealer_price') border-red-300 bg-red-50 @enderror" placeholder="0.00" required>
                    <span class="helper-text">Shown to approved dealers only.</span>
                    @error('dealer_price')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="side-field-card">
                    <span class="form-label">Number of Stock *</span>
                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" min="0" step="1" class="product-input @error('quantity') border-red-300 bg-red-50 @enderror" required>
                    <!-- <span class="helper-text">Stock status is calculated automatically from quantity.</span> -->
                    @error('quantity')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-3">
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-4 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                <span class="material-symbols-outlined !text-[20px]">save</span>
                {{ $isEditing ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                Cancel
            </a>
        </div>
    </aside>
</div>

<style>
    .product-panel {
        border-radius: 1rem;
        border: 1px solid rgb(226 232 240);
        background: white;
        padding: 1.5rem;
        box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
    }

    .product-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 2rem;
        align-items: start;
    }

    @media (min-width: 1280px) {
        .product-form-layout {
            grid-template-columns: minmax(0, 1fr) 360px;
        }
    }

    .product-form-stack {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        min-width: 0;
    }

    @media (min-width: 1280px) {
        .product-form-sidebar {
            position: sticky;
            top: 1.5rem;
        }
    }

    .panel-heading {
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .panel-heading > .material-symbols-outlined {
        display: inline-flex;
        width: 2.5rem;
        height: 2.5rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        background: rgba(93, 92, 255, 0.1);
        color: #5D5CFF;
    }

    .panel-heading h2 {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: rgb(15 23 42);
    }

    .panel-heading p,
    .helper-text {
        font-size: 0.75rem;
        color: rgb(100 116 139);
    }

    .form-label {
        margin-left: 0.25rem;
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgb(71 85 105);
    }

    .product-input {
        width: 100%;
        display: block;
        border-radius: 0.75rem;
        border: 1px solid rgb(203 213 225);
        background: white;
        padding: 0.75rem;
        font-size: 0.9375rem;
        outline: none;
        transition: all 150ms ease;
    }

    .product-input:focus {
        border-color: #5D5CFF;
        box-shadow: 0 0 0 3px rgba(93, 92, 255, 0.14);
    }

    .field-error {
        display: block;
        min-height: 1rem;
        padding-left: 0.25rem;
        font-size: 0.75rem;
        color: rgb(220 38 38);
    }

    .category-picker {
        max-height: 14rem;
        overflow: auto;
        border-radius: 0.75rem;
        border: 1px solid rgb(203 213 225);
        background: white;
        padding: 0.5rem;
        margin-top: 0.75rem;
    }

    .category-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-radius: 0.625rem;
        padding: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: rgb(30 41 59);
        transition: background 150ms ease;
    }

    .category-option:hover {
        background: rgb(248 250 252);
    }

    .category-option input {
        width: 1.1rem;
        height: 1.1rem;
        border-radius: 0.25rem;
        accent-color: #5D5CFF;
    }

    .category-select-all {
        border-bottom: 1px solid rgb(226 232 240);
        margin-bottom: 0.25rem;
    }

    .image-dropzone {
        display: flex;
        min-height: 15rem;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.9rem;
        border-radius: 1rem;
        border: 2px dashed rgb(203 213 225);
        background: linear-gradient(180deg, rgb(248 250 252), white);
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 150ms ease;
    }

    .image-dropzone:hover,
    .image-dropzone.is-dragging {
        border-color: #5D5CFF;
        background: rgba(93, 92, 255, 0.04);
    }

    .upload-icon {
        font-size: 3rem;
        color: rgb(148 163 184);
    }

    .dropzone-title {
        font-size: 1rem;
        font-weight: 600;
        color: rgb(71 85 105);
    }

    .browse-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0.75rem;
        background: #5D5CFF;
        padding: 0.85rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 800;
        color: white;
    }

    .image-card,
    .preview-card {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(248 250 252);
    }

    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.25rem;
    }

    .image-preview-grid.hidden {
        display: none;
    }

    .image-card img,
    .preview-card img {
        height: 9.5rem;
        width: 100%;
        object-fit: cover;
    }

    .image-card span,
    .preview-card span {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.65rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: rgb(71 85 105);
    }

    .preview-remove {
        position: absolute;
        right: 0.65rem;
        top: 0.65rem;
        display: inline-flex;
        height: 2rem;
        width: 2rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        background: rgb(239 68 68);
        color: white;
        box-shadow: 0 10px 20px -12px rgba(127, 29, 29, 0.8);
    }

    .existing-remove-button {
        position: absolute;
        right: 0.65rem;
        top: 0.65rem;
        display: inline-flex !important;
        height: 2rem;
        width: 2rem;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.5rem;
        background: rgb(239 68 68);
        color: white !important;
        padding: 0 !important;
        box-shadow: 0 10px 20px -12px rgba(127, 29, 29, 0.8);
    }

    .existing-remove-button input {
        position: absolute;
        opacity: 0;
    }

    .existing-remove-button:has(input:checked) {
        background: rgb(127 29 29);
        outline: 3px solid rgba(239, 68, 68, 0.25);
    }

    .editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        border: 1px solid rgb(203 213 225);
        border-bottom: 0;
        border-radius: 0.75rem 0.75rem 0 0;
        background: rgb(248 250 252);
        padding: 0.6rem;
    }

    .editor-button {
        display: inline-flex;
        height: 2.25rem;
        width: 2.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        color: rgb(51 65 85);
        transition: all 150ms ease;
    }

    .editor-button:hover {
        background: white;
        color: #5D5CFF;
    }

    .rich-editor {
        min-height: 15rem;
        border: 1px solid rgb(203 213 225);
        border-radius: 0 0 0.75rem 0.75rem;
        background: white;
        padding: 1rem;
        font-size: 0.9375rem;
        line-height: 1.7;
        outline: none;
    }

    .rich-editor-small {
        min-height: 9rem;
    }

    .rich-editor:focus {
        border-color: #5D5CFF;
        box-shadow: 0 0 0 3px rgba(93, 92, 255, 0.14);
    }

    .setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: rgb(51 65 85);
    }

    .sidebar-field-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .side-field-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.35rem;
        border-radius: 1rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(248 250 252);
        padding: 0.875rem;
    }

    .side-field-card.setting-row {
        flex-direction: row;
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

<script>
    (() => {
        const categoryOptions = Array.from(document.querySelectorAll('[data-category-option]'));
        const selectAll = document.getElementById('category-select-all');
        const editorPairs = [
            ['description-editor', 'description-input'],
            ['advantages-editor', 'advantages-input'],
            ['material-notes-editor', 'material-notes-input'],
        ].map(([editorId, inputId]) => ({
            editor: document.getElementById(editorId),
            input: document.getElementById(inputId),
        }));
        const form = document.querySelector('form');
        const imageInput = document.getElementById('product-images-input');
        const previewGrid = document.getElementById('selected-image-preview');
        const dropzone = document.querySelector('.image-dropzone');

        let selectedFiles = [];

        const syncImageInput = () => {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file) => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        };

        const renderPreviews = () => {
            previewGrid.innerHTML = '';
            previewGrid.classList.toggle('hidden', selectedFiles.length === 0);

            selectedFiles.forEach((file, index) => {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const card = document.createElement('div');
                card.className = 'preview-card';
                card.innerHTML = `
                    <img src="${URL.createObjectURL(file)}" alt="">
                    <button type="button" class="preview-remove" data-preview-remove="${index}" aria-label="Remove image">
                        <span class="material-symbols-outlined !text-[18px]">close</span>
                    </button>
                    <span>
                        <span class="truncate">${file.name}</span>
                        <span>${Math.round(file.size / 1024)} KB</span>
                    </span>
                `;
                previewGrid.appendChild(card);
            });
        };

        selectAll?.addEventListener('change', () => {
            categoryOptions.forEach((option) => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                checkbox.checked = selectAll.checked;
            });
        });

        document.querySelectorAll('[data-editor-command]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetEditorId = button.dataset.editorTarget || 'description-editor';
                const editor = document.getElementById(targetEditorId);
                const pair = editorPairs.find((item) => item.editor === editor);

                editor.focus();
                document.execCommand(button.dataset.editorCommand, false, null);
                if (pair?.input) {
                    pair.input.value = editor.innerHTML;
                }
            });
        });

        editorPairs.forEach(({ editor, input }) => {
            editor?.addEventListener('input', () => {
                input.value = editor.innerHTML;
            });
        });

        form?.addEventListener('submit', () => {
            editorPairs.forEach(({ editor, input }) => {
                if (editor && input) {
                    input.value = editor.innerHTML;
                }
            });
        });

        imageInput?.addEventListener('change', () => {
            selectedFiles = Array.from(imageInput.files || []);
            renderPreviews();
        });

        previewGrid?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-preview-remove]');
            if (!button) {
                return;
            }

            selectedFiles.splice(Number(button.dataset.previewRemove), 1);
            syncImageInput();
            renderPreviews();
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropzone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone?.addEventListener('drop', (event) => {
            if (!event.dataTransfer?.files?.length) {
                return;
            }

            imageInput.files = event.dataTransfer.files;
            selectedFiles = Array.from(imageInput.files || []);
            renderPreviews();
        });
    })();
</script>
