<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ResumeStorageService
{
    public function store(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            throw new RuntimeException('Resume upload failed integrity checks.');
        }

        $directory = 'uploads/resumes/'.now()->format('Y').'/'.now()->format('m');
        $fileName = Str::uuid()->toString().'.pdf';
        $path = Storage::disk('local')->putFileAs($directory, $file, $fileName);

        if (! $path) {
            throw new RuntimeException('Unable to store resume.');
        }

        return [
            'resume_file_name' => $this->cleanOriginalName($file->getClientOriginalName()),
            'resume_file_path' => $path,
            'resume_file_size' => $file->getSize(),
            'resume_mime_type' => $file->getMimeType() ?: 'application/pdf',
        ];
    }

    private function cleanOriginalName(string $name): string
    {
        $baseName = basename(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $name));

        return Str::limit(preg_replace('/[^A-Za-z0-9._ -]/', '', $baseName) ?: 'resume.pdf', 255, '');
    }
}
