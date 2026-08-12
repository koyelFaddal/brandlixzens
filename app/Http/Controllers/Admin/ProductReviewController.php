<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $reviews = ProductReview::query()
            ->with([
                'user:id,name,email',
                'product:id,name,sku',
                'order:id,order_number',
                'orderItem:id,product_name,product_sku',
                'images',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'))
                        ->orWhereHas('product', fn ($productQuery) => $productQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('sku', 'like', '%'.$search.'%'))
                        ->orWhereHas('order', fn ($orderQuery) => $orderQuery
                            ->where('order_number', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.reviews.add');
    }

    public function show(ProductReview $review): View
    {
        $review->load([
            'user:id,name,email,phone',
            'product:id,name,sku,price',
            'order:id,order_number,ordered_at',
            'orderItem:id,product_name,product_sku,unit_price,quantity,line_total',
            'images',
        ]);

        return view('admin.reviews.show', [
            'review' => $review,
            'averageRating' => $this->averageRating($review),
        ]);
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->load('images');

        foreach ($review->images as $image) {
            if ($image->image) {
                Storage::disk('public')->delete($image->image);
            }
        }

        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('status', 'Review deleted successfully.');
    }

    private function averageRating(ProductReview $review): float
    {
        return round(collect([
            $review->overall_rating,
            $review->quality_rating,
            $review->material_rating,
            $review->delivery_rating,
        ])->filter()->avg() ?: 0, 1);
    }
}
