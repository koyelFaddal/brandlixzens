<?php

namespace App\Services;

use App\Models\ErrorLog;
use Throwable;

class ErrorLogService
{
    public function record(Throwable $exception, string $shortMessage, ?int $leadId = null): void
    {
        $this->create(
            filePath: $exception->getFile(),
            lineNumber: $exception->getLine(),
            shortMessage: $shortMessage,
            fullErrorMessage: $exception->getMessage().PHP_EOL.$exception->getTraceAsString(),
            leadId: $leadId,
        );
    }

    public function recordMessage(string $shortMessage, string $fullErrorMessage, ?string $filePath = null, ?int $lineNumber = null, ?int $leadId = null): void
    {
        $this->create(
            filePath: $filePath ?: 'job_application_system',
            lineNumber: $lineNumber ?: 0,
            shortMessage: $shortMessage,
            fullErrorMessage: $fullErrorMessage,
            leadId: $leadId,
        );
    }

    private function create(string $filePath, int $lineNumber, string $shortMessage, string $fullErrorMessage, ?int $leadId): void
    {
        try {
            ErrorLog::create([
                'lead_id' => $leadId,
                'file_path' => $filePath,
                'line_number' => max(0, $lineNumber),
                'short_message' => mb_substr($shortMessage, 0, 255),
                'full_error_message' => $fullErrorMessage,
            ]);
        } catch (Throwable) {
            // Avoid masking the original application error if error logging itself fails.
        }
    }
}
