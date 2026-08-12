<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SupportTicketAttachmentStorageService
{
    public function store(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            throw new RuntimeException('Attachment upload failed integrity checks.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $directory = 'uploads/support-tickets/'.now()->format('Y').'/'.now()->format('m');
        $fileName = Str::uuid()->toString().'.'.$extension;
        $path = Storage::disk('local')->putFileAs($directory, $file, $fileName);

        if (! $path) {
            throw new RuntimeException('Unable to store support ticket attachment.');
        }

        return [
            'attachment_name' => $this->cleanOriginalName($file->getClientOriginalName()),
            'attachment_path' => $path,
            'attachment_type' => $file->getMimeType() ?: 'application/octet-stream',
            'attachment_size' => $file->getSize() ?: 0,
        ];
    }

    private function cleanOriginalName(string $name): string
    {
        $baseName = basename(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $name));

        return Str::limit(preg_replace('/[^A-Za-z0-9._ -]/', '', $baseName) ?: 'attachment', 255, '');
    }
}
