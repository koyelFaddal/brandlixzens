<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use App\Services\ErrorLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\Support\Str;

class AiJobController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $jobs = AiJob::query()
            ->select([
                'id',
                'job_title',
                'category',
                'work_mode',
                'work_location',
                'employment_type',
                'job_post_date',
                'preview_image',
                'created_at',
                'updated_at',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('job_title', 'like', $search.'%')
                        ->orWhere('work_location', 'like', $search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $jobs->getCollection()->transform(fn (AiJob $job): array => $this->formatJob($job));

        return response()->json([
            'status' => 'success',
            'message' => 'Jobs loaded successfully',
            'data' => $jobs->items(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules(true));
        $this->validateOverviewWordLimit($validator);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $validated = $validator->validated();

        try {
            $path = $this->storePreviewImage($request);
            $job = AiJob::create($this->jobPayload($validated, (int) date('U'), $path));

            return response()->json([
                'status' => 'success',
                'message' => 'Job saved successfully',
                'data' => $this->formatJob($job->fresh()),
            ]);
        } catch (\Throwable $exception) {
            if (! empty($path)) Storage::disk('public')->delete($path);
            Log::error('Admin job save failed', ['exception' => $exception]);
            $this->errorLog->record($exception, 'Admin job save failed');

            return $this->serverError('Unable to save job. Please try again.');
        }
    }

    public function show(AiJob $aiJob): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Job loaded successfully',
            'data' => $this->formatJob($aiJob),
        ]);
    }

    public function update(Request $request, AiJob $aiJob): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules(false));
        $this->validateOverviewWordLimit($validator);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $validated = $validator->validated();
        $newImage = null;
        try {
            if ($request->hasFile('preview_image')) {
                $newImage = $this->storePreviewImage($request);
            }
            $oldImage = $aiJob->preview_image;
            $aiJob->update($this->jobPayload($validated, $aiJob->job_post_date, $newImage ?: $oldImage));
            if ($newImage && $oldImage) Storage::disk('public')->delete($oldImage);

            return response()->json([
                'status' => 'success',
                'message' => 'Job updated successfully',
                'data' => $this->formatJob($aiJob->fresh()),
            ]);
        } catch (\Throwable $exception) {
            if ($newImage) Storage::disk('public')->delete($newImage);
            Log::error('Admin job update failed', ['exception' => $exception]);
            $this->errorLog->record($exception, 'Admin job update failed');

            return $this->serverError('Unable to update job. Please try again.');
        }
    }

    public function destroy(AiJob $aiJob): JsonResponse
    {
        try {
            if ($aiJob->preview_image) Storage::disk('public')->delete($aiJob->preview_image);
            $aiJob->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Job deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Admin job delete failed', ['exception' => $exception]);
            $this->errorLog->record($exception, 'Admin job delete failed');

            return $this->serverError('Unable to delete job. Please try again.');
        }
    }

    private function rules(bool $creating): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:development,design,marketing,customer-service,operations,finance,management'],
            'work_mode' => ['required', 'in:100% Remote,Hybrid,On-site'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:Full-time,Part-time,Contract,Internship'],
            'overview' => ['required', 'string'],
            'responsibilities' => ['required', 'string'],
            'required_skills' => ['required', 'string'],
            'preferred_skills' => ['required', 'string'],
            'experience_required' => ['required', 'string', 'max:255'],
            'preview_image' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    private function jobPayload(array $validated, int $jobPostDate, ?string $previewImage): array
    {
        return [
            'job_title' => $validated['job_title'],
            'category' => $validated['category'],
            'work_mode' => $validated['work_mode'],
            'work_location' => filled($validated['work_location'] ?? null)
                ? trim($validated['work_location'])
                : $validated['work_mode'],
            'employment_type' => $validated['employment_type'],
            'overview' => $validated['overview'],
            'responsibilities' => $validated['responsibilities'],
            'required_skills' => $validated['required_skills'],
            'preferred_skills' => $validated['preferred_skills'],
            'experience_required' => $validated['experience_required'],
            'job_post_date' => $jobPostDate,
            'preview_image' => $previewImage,
        ];
    }

    private function storePreviewImage(Request $request): ?string
    {
        if (! $request->hasFile('preview_image')) return null;
        $file = $request->file('preview_image');
        return Storage::disk('public')->putFileAs(
            'jobs/'.now()->format('Y/m'),
            $file,
            Str::uuid().'.'.$file->getClientOriginalExtension()
        );
    }

    private function validateOverviewWordLimit(ValidationValidator $validator): void
    {
        $validator->after(function (ValidationValidator $validator): void {
            $data = $validator->getData();
            $overview = trim((string) ($data['overview'] ?? ''));

            if ($overview !== '' && str_word_count($overview) > 45) {
                $validator->errors()->add('overview', 'Overview must not exceed 45 words.');
            }
        });
    }

    private function formatJob(AiJob $job): array
    {
        return [
            'id' => $job->id,
            'job_title' => $job->job_title,
            'category' => $job->category,
            'category_label' => str($job->category)->replace('-', ' ')->title()->toString(),
            'work_mode' => $job->work_mode,
            'work_location' => $job->work_location,
            'employment_type' => $job->employment_type,
            'overview' => $job->overview,
            'responsibilities' => $job->responsibilities,
            'required_skills' => $job->required_skills,
            'preferred_skills' => $job->preferred_skills,
            'experience_required' => $job->experience_required,
            'job_post_date' => $job->job_post_date,
            'job_post_date_readable' => date('M d, Y', (int) $job->job_post_date),
            'preview_image_url' => $job->preview_image ? asset('storage/'.$job->preview_image) : null,
            'created_at' => optional($job->created_at)->toDateTimeString(),
            'updated_at' => optional($job->updated_at)->toDateTimeString(),
        ];
    }

    private function validationError($validator): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Please fix the highlighted fields.',
            'errors' => $validator->errors(),
        ], 422);
    }

    private function serverError(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => [],
        ], 500);
    }
}
