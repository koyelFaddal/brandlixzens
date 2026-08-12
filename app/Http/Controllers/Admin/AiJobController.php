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
                'work_location',
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

            $job = AiJob::create($this->jobPayload($validated, $path, (int) date('U')));

            return response()->json([
                'status' => 'success',
                'message' => 'Job saved successfully',
                'data' => $this->formatJob($job->fresh()),
            ]);
        } catch (\Throwable $exception) {
            if (isset($path) && $path) {
                Storage::disk('public')->delete($path);
            }

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
        $oldImage = $aiJob->preview_image;
        $newImage = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('preview_image')) {
                $newImage = $this->storePreviewImage($request);

                if ($oldImage && ! Storage::disk('public')->delete($oldImage)) {
                    Storage::disk('public')->delete($newImage);
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to replace old preview image.',
                        'errors' => [],
                    ], 500);
                }
            }

            $aiJob->update($this->jobPayload($validated, $newImage ?? $oldImage, $aiJob->job_post_date));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Job updated successfully',
                'data' => $this->formatJob($aiJob->fresh()),
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            Log::error('Admin job update failed', ['exception' => $exception]);
            $this->errorLog->record($exception, 'Admin job update failed');

            return $this->serverError('Unable to update job. Please try again.');
        }
    }

    public function destroy(AiJob $aiJob): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($aiJob->preview_image && ! Storage::disk('public')->delete($aiJob->preview_image)) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to delete preview image. Job was not deleted.',
                    'errors' => [],
                ], 500);
            }

            $aiJob->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Job deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Admin job delete failed', ['exception' => $exception]);
            $this->errorLog->record($exception, 'Admin job delete failed');

            return $this->serverError('Unable to delete job. Please try again.');
        }
    }

    private function rules(bool $creating): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'work_location' => ['required', 'string', 'max:255'],
            'overview' => ['required', 'string'],
            'responsibilities' => ['required', 'string'],
            'required_skills' => ['required', 'string'],
            'preferred_skills' => ['required', 'string'],
            'experience_required' => ['required', 'string', 'max:255'],
            'preview_image' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    private function jobPayload(array $validated, ?string $previewImage, int $jobPostDate): array
    {
        return [
            'job_title' => $validated['job_title'],
            'work_location' => $validated['work_location'],
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
        if (! $request->hasFile('preview_image')) {
            return null;
        }

        $file = $request->file('preview_image');
        $directory = 'jobs/'.now()->format('Y').'/'.now()->format('m');
        $filename = (string) Str::uuid().'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs($directory, $file, $filename);
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
            'work_location' => $job->work_location,
            'overview' => $job->overview,
            'responsibilities' => $job->responsibilities,
            'required_skills' => $job->required_skills,
            'preferred_skills' => $job->preferred_skills,
            'experience_required' => $job->experience_required,
            'job_post_date' => $job->job_post_date,
            'job_post_date_readable' => date('M d, Y', (int) $job->job_post_date),
            'preview_image' => $job->preview_image ? 'storage/'.$job->preview_image : null,
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
