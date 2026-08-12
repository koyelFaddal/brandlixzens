<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $categories = Category::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $image = null;

        try {
            $image = $this->storeImage($request);

            Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $image,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('status', 'Category created successfully.');
        } catch (\Throwable $exception) {
            if ($image) {
                Storage::disk('public')->delete($image);
            }

            return back()
                ->withInput()
                ->withErrors(['image' => 'Unable to save category. Please try again.']);
        }
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate($this->rules($category));
        $oldImage = $category->image;
        $newImage = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $newImage = $this->storeImage($request);
            }

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $newImage ?? $oldImage,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();

            if ($newImage && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('status', 'Category updated successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            return back()
                ->withInput()
                ->withErrors(['image' => 'Unable to update category. Please try again.']);
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        $image = $category->image;

        try {
            DB::beginTransaction();
            $category->delete();
            DB::commit();

            if ($image) {
                Storage::disk('public')->delete($image);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('status', 'Category deleted successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();

            return back()->withErrors(['category' => 'Unable to delete category. Please try again.']);
        }
    }

    public function updateStatus(Category $category): RedirectResponse
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return back()->with('status', 'Category status updated successfully.');
    }

    private function rules(?Category $category = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => [$category ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $directory = 'categories/'.now()->format('Y').'/'.now()->format('m');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = Str::uuid().'.'.$extension;

        return Storage::disk('public')->putFileAs($directory, $file, $filename);
    }
}
