<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CtaLead;
use App\Services\ErrorLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CtaLeadController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request, int $sourceType): JsonResponse
    {
        abort_unless(array_key_exists($sourceType, self::sourceLabels()), 404);

        $search = trim((string) $request->query('search', ''));

        $leads = CtaLead::query()
            ->select(['id', 'name', 'email', 'phone', 'city', 'source_type', 'cta_button', 'created_at'])
            ->where('source_type', $sourceType)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $leads->getCollection()->transform(fn (CtaLead $lead): array => $this->formatLead($lead));

        return response()->json([
            'status' => 'success',
            'message' => 'CTA leads loaded successfully',
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'from' => $leads->firstItem(),
                'to' => $leads->lastItem(),
            ],
        ]);
    }

    public function destroy(CtaLead $ctaLead): JsonResponse
    {
        try {
            $ctaLead->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'CTA lead deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin CTA lead delete failed');

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete CTA lead. Please try again.',
                'errors' => [],
            ], 500);
        }
    }

    public static function sourceLabels(): array
    {
        return [
            1 => 'AI Research',
            2 => 'AI Resources',
            3 => 'AI News',
            4 => 'AI Community',
            5 => 'Industries',
            6 => 'Case Studies',
            7 => 'Blog / Insights',
        ];
    }

    private function formatLead(CtaLead $lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'city' => $lead->city,
            'source_type' => $lead->source_type,
            'source_label' => self::sourceLabels()[$lead->source_type] ?? 'Unknown',
            'cta_button' => $lead->cta_button,
            'created_date' => optional($lead->created_at)->format('M d, Y'),
            'created_at' => optional($lead->created_at)->toDateTimeString(),
        ];
    }
}
