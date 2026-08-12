<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCtaLead;
use App\Services\ErrorLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AdvancedCtaLeadController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request, int $menuType): JsonResponse
    {
        abort_unless(array_key_exists($menuType, self::menuLabels()), 404);

        $search = trim((string) $request->query('search', ''));

        $leads = AdvancedCtaLead::query()
            ->select(['id', 'name', 'email', 'phone', 'city', 'company_name', 'message', 'uploaded_document', 'menu_type', 'created_at'])
            ->where('menu_type', $menuType)
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

        $leads->getCollection()->transform(fn (AdvancedCtaLead $lead): array => $this->formatLead($lead));

        return response()->json([
            'status' => 'success',
            'message' => 'Advanced CTA leads loaded successfully',
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

    public function document(AdvancedCtaLead $advancedCtaLead)
    {
        abort_unless($advancedCtaLead->uploaded_document, 404);
        abort_unless(Storage::disk('public')->exists($advancedCtaLead->uploaded_document), 404);

        return Storage::disk('public')->download(
            $advancedCtaLead->uploaded_document,
            basename($advancedCtaLead->uploaded_document)
        );
    }

    public function bulkDocumentDownload(Request $request)
    {
        $ids = collect($request->input('advanced_cta_leads', []))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $menuType = (int) $request->input('menu_type');

        if ($ids->isEmpty() || ! array_key_exists($menuType, self::menuLabels())) {
            return back()->with('error', 'Please select at least one advanced CTA lead.');
        }

        $leads = AdvancedCtaLead::query()
            ->select(['id', 'name', 'uploaded_document', 'menu_type'])
            ->where('menu_type', $menuType)
            ->whereIn('id', $ids)
            ->get();

        $validLeads = $leads->filter(
            fn (AdvancedCtaLead $lead): bool => $lead->uploaded_document
                && Storage::disk('public')->exists($lead->uploaded_document)
        );

        if ($validLeads->isEmpty()) {
            return back()->with('error', 'No uploaded document files were found for the selected leads.');
        }

        $zip = new ZipArchive();
        $zipDirectory = storage_path('app/temp');
        $zipFileName = $this->zipDownloadName($menuType);
        $zipPath = $zipDirectory.'/'.$zipFileName;

        if (! is_dir($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to prepare selected documents for download.');
        }

        $usedNames = [];
        foreach ($validLeads as $lead) {
            $documentPath = Storage::disk('public')->path($lead->uploaded_document);
            $zip->addFile($documentPath, $this->documentZipName($lead, $usedNames));
        }

        $zip->close();

        return response()
            ->download($zipPath, $zipFileName, ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    public function destroy(AdvancedCtaLead $advancedCtaLead): JsonResponse
    {
        try {
            $documentPath = $advancedCtaLead->uploaded_document;
            $advancedCtaLead->delete();

            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Advanced CTA lead deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin advanced CTA lead delete failed');

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete advanced CTA lead. Please try again.',
                'errors' => [],
            ], 500);
        }
    }

    public static function menuLabels(): array
    {
        return AdvancedCtaLead::MENU_TYPES;
    }

    private function formatLead(AdvancedCtaLead $lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'city' => $lead->city,
            'company_name' => $lead->company_name,
            'message' => $lead->message,
            'uploaded_document' => $lead->uploaded_document,
            'document_name' => $lead->uploaded_document ? basename($lead->uploaded_document) : null,
            'document_url' => $lead->uploaded_document
                ? route('admin.cta.advanced-leads.document', ['advancedCtaLead' => $lead->id])
                : null,
            'menu_type' => $lead->menu_type,
            'menu_label' => self::menuLabels()[$lead->menu_type] ?? 'Unknown',
            'created_date' => optional($lead->created_at)->format('M d, Y'),
            'created_at' => optional($lead->created_at)->toDateTimeString(),
        ];
    }

    private function zipDownloadName(int $menuType): string
    {
        $label = self::menuLabels()[$menuType] ?? 'advanced-cta';
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $label), '-')) ?: 'advanced-cta';

        return $slug.'-documents-'.now()->format('Ymd-His').'-'.substr((string) uniqid(), -6).'.zip';
    }

    private function documentZipName(AdvancedCtaLead $lead, array &$usedNames): string
    {
        $fileName = basename((string) $lead->uploaded_document);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME) ?: 'document-'.$lead->id;
        $candidate = $lead->id.'-'.$baseName.($extension ? '.'.$extension : '');
        $counter = 2;

        while (in_array($candidate, $usedNames, true)) {
            $candidate = $lead->id.'-'.$baseName.'-'.$counter.($extension ? '.'.$extension : '');
            $counter++;
        }

        $usedNames[] = $candidate;

        return $candidate;
    }
}
