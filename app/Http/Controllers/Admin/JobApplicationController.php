<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\ErrorLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class JobApplicationController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $selectedFilter = (string) $request->query('filter', 'all');

        $applications = JobApplication::query()
            ->select([
                'id',
                'job_id',
                'application_type',
                'job_title_snapshot',
                'full_name',
                'email',
                'phone_number',
                'city',
                'major_experience',
                'years_of_experience',
                'present_salary',
                'notice_period',
                'resume_file_path',
                'created_at',
            ])
            ->when($selectedFilter === 'future_opening', function ($query): void {
                $query->where('application_type', 'future_opening');
            })
            ->when(is_numeric($selectedFilter), function ($query) use ($selectedFilter): void {
                $query->where('job_id', (int) $selectedFilter);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('city', 'like', '%'.$search.'%')
                        ->orWhere('major_experience', 'like', '%'.$search.'%')
                        ->orWhere('years_of_experience', 'like', '%'.$search.'%')
                        ->orWhere('present_salary', 'like', '%'.$search.'%')
                        ->orWhere('notice_period', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $applications->getCollection()->transform(fn (JobApplication $application): array => $this->formatApplication($application));

        return response()->json([
            'status' => 'success',
            'message' => 'Applications loaded successfully',
            'data' => $applications->items(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
            ],
        ]);
    }

    public function show(JobApplication $jobApplication): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Application loaded successfully',
            'data' => $this->formatApplication($jobApplication, true),
        ]);
    }

    public function resume(JobApplication $jobApplication)
    {
        if (! Storage::disk('local')->exists($jobApplication->resume_file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $jobApplication->resume_file_path,
            $jobApplication->resume_file_name,
            ['Content-Type' => $jobApplication->resume_mime_type]
        );
    }

    public function bulkResumeDownload(Request $request)
    {
        $ids = collect($request->input('applications', []))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', 'Please select at least one application.');
        }

        $applications = JobApplication::query()
            ->whereIn('id', $ids)
            ->get();

        $validApplications = $applications->filter(
            fn (JobApplication $application): bool => $application->resume_file_path
                && Storage::disk('local')->exists($application->resume_file_path)
        );

        if ($validApplications->isEmpty()) {
            return back()->with('error', 'No valid resume files were found for the selected applications.');
        }

        $zip = new ZipArchive();
        $zipDirectory = storage_path('app/temp');
        $zipPath = $zipDirectory.'/selected-resumes-'.now()->format('YmdHis').'.zip';

        if (! is_dir($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to prepare selected resumes for download.');
        }

        foreach ($validApplications as $application) {
            $resumePath = Storage::disk('local')->path($application->resume_file_path);
            $fileName = $this->resumeZipName($application);
            $zip->addFile($resumePath, $fileName);
        }

        $zip->close();

        return response()
            ->download($zipPath, 'selected-resumes.zip', ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    public function destroy(JobApplication $jobApplication): JsonResponse
    {
        try {
            $resumePath = $jobApplication->resume_file_path;

            if ($resumePath && Storage::disk('local')->exists($resumePath)) {
                Storage::disk('local')->delete($resumePath);
            }

            $jobApplication->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Application deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin application delete failed');

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete application. Please try again.',
                'errors' => [],
            ], 500);
        }
    }

    private function formatApplication(JobApplication $application, bool $includeDetails = false): array
    {
        $type = $application->application_type === 'future_opening' ? 'Future Opening' : 'Job Application';

        $data = [
            'id' => $application->id,
            'job_id' => $application->job_id,
            'application_type' => $application->application_type,
            'application_type_label' => $type,
            'job_title' => $application->job_title_snapshot,
            'full_name' => $application->full_name,
            'email' => $application->email,
            'phone_number' => $application->phone_number,
            'city' => $application->city,
            'major_experience' => $application->major_experience,
            'present_salary' => $application->present_salary,
            'notice_period' => $application->notice_period,
            'applied_date' => optional($application->created_at)->format('M d, Y'),
            'applied_at' => optional($application->created_at)->toDateTimeString(),
            'has_resume' => $application->resume_file_path && Storage::disk('local')->exists($application->resume_file_path),
            'resume_url' => route('admin.career.applications.resume', ['jobApplication' => $application->id]),
        ];

        if (! $includeDetails) {
            return $data;
        }

        return [
            ...$data,
            'portfolio_url' => $application->portfolio_url,
            'years_of_experience' => $application->years_of_experience,
            'current_role' => $application->current_role,
            'skills' => $application->skills,
            'ai_challenge' => $application->ai_challenge,
            'resume_file_name' => $application->resume_file_name,
            'resume_file_size' => $application->resume_file_size,
            'resume_mime_type' => $application->resume_mime_type,
            'email_notification_sent_at' => optional($application->email_notification_sent_at)->toDateTimeString(),
            'email_notification_error' => $application->email_notification_error,
        ];
    }

    private function resumeZipName(JobApplication $application): string
    {
        $name = preg_replace('/[^A-Za-z0-9\-_]+/', '-', $application->full_name);
        $name = trim((string) $name, '-') ?: 'application-'.$application->id;
        $extension = pathinfo($application->resume_file_name ?: '', PATHINFO_EXTENSION) ?: 'pdf';

        return $application->id.'-'.$name.'.'.$extension;
    }
}
