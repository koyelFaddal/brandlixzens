<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Load resources with title search and 10-item pagination.
     * Only table fields are selected so large datasets stay fast.
     */
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $resources = AiResource::query()
            ->select([
                'id',
                'title',
                'description',
                'file',
                'file_original_name',
                'file_size',
                'file_mime_type',
                'file_extension',
                'preview_image',
                'post_date',
                'created_at',
                'updated_at',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('title', 'like', $search.'%');
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $resources->getCollection()->transform(fn (AiResource $resource): array => $this->formatResource($resource));

        return response()->json([
            'status' => 'success',
            'message' => 'Resources loaded successfully',
            'data' => $resources->items(),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'per_page' => $resources->perPage(),
                'total' => $resources->total(),
                'from' => $resources->firstItem(),
                'to' => $resources->lastItem(),
            ],
        ]);
    }

    /**
     * Store a new resource with its preview image and document file.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules(true));
        $this->validateDescriptionWordLimit($validator);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $validated = $validator->validated();
        $resourceFile = null;
        $previewImage = null;

        try {
            $resourceFile = $this->storeResourceFile($request);
            $previewImage = $this->storePreviewImage($request);

            $resource = AiResource::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'file' => $resourceFile['path'],
                'file_original_name' => $resourceFile['original_name'],
                'file_size' => $resourceFile['size'],
                'file_mime_type' => $resourceFile['mime_type'],
                'file_extension' => $resourceFile['extension'],
                'preview_image' => $previewImage,
                'post_date' => now()->timestamp,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Resource saved successfully',
                'data' => $this->formatResource($resource->fresh()),
            ]);
        } catch (\Throwable $exception) {
            if (! empty($resourceFile['path'])) {
                Storage::disk('public')->delete($resourceFile['path']);
            }

            if ($previewImage) {
                Storage::disk('public')->delete($previewImage);
            }

            return $this->serverError('Unable to save resource. Please try again.');
        }
    }

    /**
     * Return one resource for the read-only view and edit modal.
     */
    public function show(AiResource $aiResource): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Resource loaded successfully',
            'data' => $this->formatResource($aiResource),
        ]);
    }

    /**
     * Update a resource. Replaced files are removed from storage after save.
     */
    public function update(Request $request, AiResource $aiResource): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules(false));
        $this->validateDescriptionWordLimit($validator);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $validated = $validator->validated();
        $oldFile = $aiResource->file;
        $oldPreviewImage = $aiResource->preview_image;
        $newFile = null;
        $newPreviewImage = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('file')) {
                $newFile = $this->storeResourceFile($request);
            }

            if ($request->hasFile('preview_image')) {
                $newPreviewImage = $this->storePreviewImage($request);
            }

            $payload = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'preview_image' => $newPreviewImage ?? $oldPreviewImage,
                'post_date' => now()->timestamp,
            ];

            if ($newFile) {
                $payload['file'] = $newFile['path'];
                $payload['file_original_name'] = $newFile['original_name'];
                $payload['file_size'] = $newFile['size'];
                $payload['file_mime_type'] = $newFile['mime_type'];
                $payload['file_extension'] = $newFile['extension'];
            }

            $aiResource->update($payload);

            DB::commit();

            if ($newFile && $oldFile) {
                Storage::disk('public')->delete($oldFile);
            }

            if ($newPreviewImage && $oldPreviewImage) {
                Storage::disk('public')->delete($oldPreviewImage);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Resource updated successfully',
                'data' => $this->formatResource($aiResource->fresh()),
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            if (! empty($newFile['path'])) {
                Storage::disk('public')->delete($newFile['path']);
            }

            if ($newPreviewImage) {
                Storage::disk('public')->delete($newPreviewImage);
            }

            return $this->serverError('Unable to update resource. Please try again.');
        }
    }

    /**
     * Delete the resource row, its document file, and its preview image.
     */
    public function destroy(AiResource $aiResource): JsonResponse
    {
        $file = $aiResource->file;
        $previewImage = $aiResource->preview_image;

        try {
            DB::beginTransaction();

            $aiResource->delete();

            DB::commit();

            if ($file) {
                Storage::disk('public')->delete($file);
            }

            if ($previewImage) {
                Storage::disk('public')->delete($previewImage);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Resource deleted successfully',
                'data' => [],
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return $this->serverError('Unable to delete resource. Please try again.');
        }
    }

    /**
     * Shared validation rules for create and update requests.
     */
    private function rules(bool $creating): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'preview_image' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'file' => [$creating ? 'required' : 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }

    /**
     * Keep resource descriptions short for table and detail readability.
     */
    private function validateDescriptionWordLimit($validator): void
    {
        $validator->after(function ($validator): void {
            $description = trim((string) ($validator->getData()['description'] ?? ''));

            if ($description !== '' && str_word_count($description) > 35) {
                $validator->errors()->add('description', 'Description must not exceed 35 words.');
            }
        });
    }
    /**
     * Store resource documents in year/month folders and keep metadata in DB.
     */
    private function storeResourceFile(Request $request): ?array
    {
        if (! $request->hasFile('file')) {
            return null;
        }

        $file = $request->file('file');
        $directory = 'resources/files/'.now()->format('Y').'/'.now()->format('m');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = (string) Str::uuid().($extension ? '.'.$extension : '');
        $path = Storage::disk('public')->putFileAs($directory, $file, $filename);

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
        ];
    }

    /**
     * Store preview images separately from downloadable resource documents.
     */
    private function storePreviewImage(Request $request): ?string
    {
        if (! $request->hasFile('preview_image')) {
            return null;
        }

        $file = $request->file('preview_image');
        $directory = 'resources/previews/'.now()->format('Y').'/'.now()->format('m');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = (string) Str::uuid().($extension ? '.'.$extension : '');

        return Storage::disk('public')->putFileAs($directory, $file, $filename);
    }

    /**
     * Format resource rows for the lightweight Blade AJAX table.
     */
    private function formatResource(AiResource $resource): array
    {
        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'description' => $resource->description,
            'title_short' => Str::limit($resource->title, 35),
            'description_short' => Str::limit($resource->description, 60),
            'file' => $resource->file,
            'file_name' => $resource->file_original_name ?: ($resource->file ? basename($resource->file) : null),
            'file_path' => $resource->file,
            'file_url' => $resource->file ? asset('storage/'.$resource->file) : null,
            'file_size' => $resource->file_size,
            'file_size_readable' => $this->readableFileSize((int) $resource->file_size),
            'file_mime_type' => $resource->file_mime_type,
            'file_extension' => $resource->file_extension,
            'preview_image' => $resource->preview_image,
            'preview_image_url' => $resource->preview_image ? asset('storage/'.$resource->preview_image) : null,
            'post_date' => $resource->post_date,
            'post_date_readable' => $resource->post_date ? date('M d, Y', (int) $resource->post_date) : null,
            'created_at' => optional($resource->created_at)->toDateTimeString(),
            'updated_at' => optional($resource->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * Convert stored bytes into a small readable label for the details modal.
     */
    private function readableFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $size = $bytes / (1024 ** $power);

        return round($size, $power === 0 ? 0 : 1).' '.$units[$power];
    }

    /**
     * Return validation errors in the format used by the page JavaScript.
     */
    private function validationError($validator): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Please fix the highlighted fields.',
            'errors' => $validator->errors(),
        ], 422);
    }

    /**
     * Keep server errors consistent for all AJAX requests.
     */
    private function serverError(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => [],
        ], 500);
    }
}