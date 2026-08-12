<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with(['categories:id,name', 'primaryImage:id,product_id,image'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => $this->activeCategories(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());
        $uploadedImages = [];

        try {
            DB::beginTransaction();

            $product = Product::create($this->payload($validated));
            $product->categories()->sync($validated['categories']);

            $uploadedImages = $this->storeImages($request, $product);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('status', 'Product created successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->deleteStoredImages($uploadedImages);

            return back()
                ->withInput()
                ->withErrors(['product' => 'Unable to save product. Please try again.']);
        }
    }

    public function show(Product $product): View
    {
        $product->load(['categories:id,name', 'images']);

        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        $product->load(['categories:id', 'images']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->activeCategories(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->rules($product), $this->messages());
        $uploadedImages = [];
        $removedImagePaths = [];

        try {
            DB::beginTransaction();

            $product->update($this->payload($validated, $product));
            $product->categories()->sync($validated['categories']);

            $removeImageIds = collect($validated['remove_images'] ?? [])
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->values()
                ->all();

            if ($removeImageIds) {
                $imagesToRemove = $product->images()
                    ->whereIn('id', $removeImageIds)
                    ->get();

                $removedImagePaths = $imagesToRemove->pluck('image')->all();
                $imagesToRemove->each->delete();
            }

            $uploadedImages = $this->storeImages($request, $product);

            DB::commit();

            $this->deleteStoredImages($removedImagePaths);

            return redirect()
                ->route('admin.products.index')
                ->with('status', 'Product updated successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->deleteStoredImages($uploadedImages);

            return back()
                ->withInput()
                ->withErrors(['product' => 'Unable to update product. Please try again.']);
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->load('images');
        $imagePaths = $product->images->pluck('image')->all();

        try {
            DB::beginTransaction();
            $product->delete();
            DB::commit();

            $this->deleteStoredImages($imagePaths);

            return redirect()
                ->route('admin.products.index')
                ->with('status', 'Product deleted successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();

            return back()->withErrors(['product' => 'Unable to delete product. Please try again.']);
        }
    }

    private function activeCategories()
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function statuses(): array
    {
        return [
            Product::STATUS_DRAFT => 'Draft',
            Product::STATUS_ACTIVE => 'Active',
            Product::STATUS_INACTIVE => 'Inactive',
        ];
    }

    private function rules(?Product $product = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'material' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'advantages' => ['nullable', 'string', 'max:2000'],
            'material_notes' => ['nullable', 'string', 'max:2000'],
            'images' => [$product ? 'nullable' : 'required', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=512,max_height=280'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', Rule::exists('product_images', 'id')->where('product_id', $product?->id ?? 0)],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'discounted_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lte:price'],
            'dealer_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'quantity' => ['required', 'integer', 'min:0'],
            'is_recommended' => ['nullable', 'boolean'],
            'expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ];
    }

    private function messages(): array
    {
        return [
            'images.*.dimensions' => 'Invalid image size. Please upload an image with dimensions 512 x 280 pixels only.',
            'discounted_price.lte' => 'The discounted price cannot be greater than the normal product price.',
        ];
    }

    private function payload(array $validated, ?Product $product = null): array
    {
        return [
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?: ($product?->sku ?? $this->generateSku($validated['name'])),
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'material' => $validated['material'] ?? null,
            'color' => $validated['color'] ?? null,
            'size' => $validated['size'] ?? null,
            'advantages' => $validated['advantages'] ?? null,
            'material_notes' => $validated['material_notes'] ?? null,
            'price' => $validated['price'],
            'discounted_price' => $validated['discounted_price'] ?? null,
            'dealer_price' => $validated['dealer_price'],
            'quantity' => $validated['quantity'],
            'is_recommended' => (bool) ($validated['is_recommended'] ?? false),
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status' => $validated['status'],
        ];
    }

    private function generateSku(string $name): string
    {
        $prefix = Str::upper(Str::slug(Str::limit($name, 24, ''), ''));
        $prefix = $prefix !== '' ? $prefix : 'PRODUCT';

        do {
            $sku = $prefix.'-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Product::query()->where('sku', $sku)->exists());

        return $sku;
    }

    private function storeImages(Request $request, Product $product): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        $stored = [];
        $nextSortOrder = (int) $product->images()->max('sort_order') + 1;

        foreach ($request->file('images') as $file) {
            $directory = 'products/'.now()->format('Y').'/'.now()->format('m');
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $filename = Str::uuid().'.'.$extension;
            $path = Storage::disk('public')->putFileAs($directory, $file, $filename);
            $stored[] = $path;

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'sort_order' => $nextSortOrder++,
            ]);
        }

        return $stored;
    }

    private function deleteStoredImages(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
