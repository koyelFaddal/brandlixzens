<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkPurchase;
use App\Services\ErrorLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulkPurchaseController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $bulkPurchases = BulkPurchase::query()
            ->select(['id', 'full_name', 'email', 'phone', 'address', 'gender', 'products_snapshot', 'created_at'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', $search.'%')
                        ->orWhere('email', 'like', $search.'%')
                        ->orWhere('phone', 'like', $search.'%')
                        ->orWhere('address', 'like', $search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $bulkPurchases->getCollection()->transform(fn (BulkPurchase $bulkPurchase): array => $this->formatBulkPurchase($bulkPurchase));

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk purchases loaded successfully',
            'data' => $bulkPurchases->items(),
            'meta' => [
                'current_page' => $bulkPurchases->currentPage(),
                'last_page' => $bulkPurchases->lastPage(),
                'per_page' => $bulkPurchases->perPage(),
                'total' => $bulkPurchases->total(),
                'from' => $bulkPurchases->firstItem(),
                'to' => $bulkPurchases->lastItem(),
            ],
        ]);
    }

    public function show(BulkPurchase $bulkPurchase): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Bulk purchase loaded successfully',
            'data' => $this->formatBulkPurchase($bulkPurchase, true),
        ]);
    }

    public function destroy(BulkPurchase $bulkPurchase): JsonResponse
    {
        try {
            $bulkPurchase->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk purchase deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin bulk purchase delete failed');

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete bulk purchase. Please try again.',
                'errors' => [],
            ], 500);
        }
    }

    private function formatBulkPurchase(BulkPurchase $bulkPurchase, bool $includeDetails = false): array
    {
        $products = $bulkPurchase->products_snapshot ?: [];

        $data = [
            'id' => $bulkPurchase->id,
            'full_name' => $bulkPurchase->full_name,
            'email' => $bulkPurchase->email,
            'phone' => $bulkPurchase->phone,
            'address' => $bulkPurchase->address,
            'gender' => $bulkPurchase->gender,
            'products_count' => count($products),
            'submitted_date' => optional($bulkPurchase->created_at)->format('M d, Y'),
            'submitted_at' => optional($bulkPurchase->created_at)->toDateTimeString(),
        ];

        if (! $includeDetails) {
            return $data;
        }

        return [
            ...$data,
            'products' => $products,
        ];
    }
}
