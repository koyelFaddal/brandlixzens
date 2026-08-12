<?php

namespace App\Services;

use App\Mail\JiraTicketCreatedForTeam;
use App\Mail\JiraTicketStatusUpdatedForUser;
use App\Models\JiraTicket;
use App\Models\JiraTicketAttachment;
use App\Models\JiraTicketEmailLog;
use App\Services\SupportTicketAttachmentStorageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class JiraTicketService
{
    public function __construct(
        private readonly JiraService $jira,
        private readonly SupportTicketAttachmentStorageService $attachmentStorage,
        private readonly ErrorLogService $errorLog,
    ) {
    }

    public function create(array $data, array $files = []): JiraTicket
    {
        $storedAttachments = [];

        try {
            Log::info('Jira support ticket create started', [
                'email' => $data['email'] ?? null,
                'subject' => $data['subject'] ?? null,
                'attachment_count' => count($files),
            ]);

            foreach ($files as $file) {
                if ($file) {
                    $storedAttachments[] = $this->attachmentStorage->store($file);
                }
            }

            Log::info('Jira support ticket local attachments stored', [
                'stored_attachment_count' => count($storedAttachments),
                'stored_attachments' => collect($storedAttachments)->pluck('attachment_name')->all(),
            ]);

            $jiraResponse = $this->jira->createTicket([
                ...$data,
                'attachments' => $storedAttachments,
            ]);

            Log::info('Jira support ticket created in Jira', [
                'jira_ticket_key' => $jiraResponse['issueKey'] ?? $jiraResponse['issue']['key'] ?? null,
                'jira_issue_id' => $jiraResponse['issueId'] ?? $jiraResponse['issue']['id'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, 'Jira support ticket failed before local database save');
            $this->deleteStoredAttachments($storedAttachments);
            throw $exception;
        }

        try {
            $ticket = JiraTicket::create([
                'jira_ticket_key' => $jiraResponse['issueKey'] ?? $jiraResponse['issue']['key'] ?? null,
                'jira_issue_id' => $jiraResponse['issueId'] ?? $jiraResponse['issue']['id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'issue_category' => $data['issue_category'],
                'priority' => $data['priority'],
                'message' => $data['message'],
                'status' => JiraTicket::STATUS_OPEN,
                'jira_response' => $jiraResponse,
            ]);

            Log::info('Jira support ticket saved in local database', [
                'ticket_id' => $ticket->id,
                'jira_ticket_key' => $ticket->jira_ticket_key,
            ]);
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, 'Jira support ticket local database save failed after Jira creation');
            $this->deleteStoredAttachments($storedAttachments);
            throw $exception;
        }

        $this->runAfterCreateStep(
            fn (): bool => $this->saveStoredAttachments($ticket, $storedAttachments),
            'Jira support ticket attachment database save failed',
            $ticket
        );

        $ticket->load('attachments');
        $this->runAfterCreateStep(
            fn (): JiraTicket => $this->syncFromJira($ticket),
            'Jira support ticket status sync after create failed',
            $ticket
        );
        $this->runAfterCreateStep(
            fn (): bool => $this->syncAttachmentsToJira($ticket),
            'Jira support ticket attachment sync after create failed',
            $ticket
        );

        $this->runAfterCreateStep(
            fn (): bool => $this->notifyTeam($ticket),
            'Jira support ticket team notification failed',
            $ticket
        );

        Log::info('Jira support ticket create completed', [
            'ticket_id' => $ticket->id,
            'jira_ticket_key' => $ticket->jira_ticket_key,
        ]);

        return $ticket;
    }

    public function updateStatus(JiraTicket $ticket, string $status): JiraTicket
    {
        $previousStatus = $ticket->status;

        DB::transaction(function () use ($ticket, $status): void {
            $jiraTransition = $this->jira->updateTicketStatus($ticket, $status);
            $ticket->forceFill([
                'status' => $jiraTransition['jira_status'] ?: $status,
                'jira_transition_response' => $jiraTransition,
            ])->save();
        });

        $this->runAfterCreateStep(
            fn (): bool => $this->notifyTicketOwner($ticket->fresh(), $previousStatus),
            'Jira support ticket owner notification failed',
            $ticket
        );

        return $ticket->fresh();
    }

    public function syncFromJira(JiraTicket $ticket): JiraTicket
    {
        if (! $ticket->jira_ticket_key) {
            return $ticket;
        }

        $jiraStatus = $this->jira->issueStatus($ticket->jira_ticket_key);

        if ($jiraStatus !== '' && $jiraStatus !== $ticket->status) {
            $ticket->forceFill(['status' => $jiraStatus])->save();
        }

        return $ticket->fresh();
    }

    public function availableTransitions(JiraTicket $ticket): array
    {
        if (! $ticket->jira_ticket_key) {
            return [];
        }

        return $this->jira->availableTransitions($ticket->jira_ticket_key);
    }

    public function attachmentDownloadUrls(JiraTicket $ticket): array
    {
        return $ticket->attachments->map(fn (JiraTicketAttachment $attachment): array => [
            'id' => $attachment->id,
            'name' => $attachment->attachment_name,
            'type' => $attachment->attachment_type,
            'size' => $attachment->attachment_size,
            'url' => URL::temporarySignedRoute(
                'support-tickets.attachments.download',
                now()->addDays(7),
                ['attachment' => $attachment->id]
            ),
        ])->all();
    }

    private function notifyTeam(JiraTicket $ticket): bool
    {
        $recipients = config('services.jira.support_team_emails', []);

        if (empty($recipients)) {
            $message = 'JIRA_SUPPORT_TEAM_EMAILS is not configured.';
            $this->safeUpdateTicket($ticket, ['team_notification_error' => $message], 'Jira ticket team notification skip state update failed');
            $this->recordMessageSafely('Jira ticket team email skipped', $message, __FILE__, __LINE__);
            return false;
        }

        foreach ($recipients as $recipient) {
            $log = $this->safeCreateEmailLog($ticket, 'ticket_created_team', $recipient, 'New Support Ticket: '.$ticket->jira_ticket_key);

            try {
                Mail::to($recipient)->send(new JiraTicketCreatedForTeam($ticket, $this->attachmentDownloadUrls($ticket)));

                $this->safeUpdateEmailLog($log, [
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]);

                $this->safeUpdateTicket($ticket, [
                    'team_notification_sent_at' => now(),
                    'team_notification_error' => null,
                ], 'Jira ticket team notification success state update failed');
            } catch (\Throwable $exception) {
                $this->safeUpdateEmailLog($log, [
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                ]);

                $this->safeUpdateTicket($ticket, ['team_notification_error' => $exception->getMessage()], 'Jira ticket team notification failure state update failed');
                $this->recordSafely($exception, 'Jira ticket team email failed');
            }
        }

        return true;
    }

    private function notifyTicketOwner(JiraTicket $ticket, string $previousStatus): bool
    {
        $log = $this->safeCreateEmailLog($ticket, 'ticket_status_user', $ticket->email, 'Support Ticket Status Updated');

        try {
            Mail::to($ticket->email)->send(new JiraTicketStatusUpdatedForUser($ticket, $previousStatus));

            $this->safeUpdateEmailLog($log, [
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            $this->safeUpdateTicket($ticket, [
                'status_notification_sent_at' => now(),
                'status_notification_error' => null,
            ], 'Jira ticket owner notification success state update failed');
        } catch (\Throwable $exception) {
            $this->safeUpdateEmailLog($log, [
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            $this->safeUpdateTicket($ticket, ['status_notification_error' => $exception->getMessage()], 'Jira ticket owner notification failure state update failed');
            $this->recordSafely($exception, 'Jira ticket user status email failed');
        }

        return true;
    }

    private function createEmailLog(JiraTicket $ticket, string $type, string $recipient, string $subject): JiraTicketEmailLog
    {
        return JiraTicketEmailLog::create([
            'jira_ticket_id' => $ticket->id,
            'type' => $type,
            'recipient' => $recipient,
            'subject' => $subject,
            'status' => 'pending',
        ]);
    }

    private function safeCreateEmailLog(JiraTicket $ticket, string $type, string $recipient, string $subject): ?JiraTicketEmailLog
    {
        try {
            return $this->createEmailLog($ticket, $type, $recipient, $subject);
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, 'Jira ticket email log create failed');

            return null;
        }
    }

    private function safeUpdateEmailLog(?JiraTicketEmailLog $log, array $attributes): void
    {
        if (! $log) {
            return;
        }

        try {
            $log->forceFill($attributes)->save();
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, 'Jira ticket email log update failed');
        }
    }

    private function safeUpdateTicket(JiraTicket $ticket, array $attributes, string $context): void
    {
        try {
            $ticket->forceFill($attributes)->save();
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, $context);
        }
    }

    private function syncAttachmentsToJira(JiraTicket $ticket): bool
    {
        $ticket->loadMissing('attachments');

        if ($ticket->attachments->isEmpty()) {
            return true;
        }

        $attachmentUrls = $this->attachmentDownloadUrls($ticket);
        $this->jira->addAttachmentReferenceComment($ticket, $attachmentUrls);

        foreach ($ticket->attachments as $attachment) {
            try {
                $jiraResponse = $this->jira->uploadAttachment($attachment);
                $firstAttachment = $jiraResponse[0] ?? [];

                $attachment->forceFill([
                    'jira_attachment_id' => $firstAttachment['id'] ?? null,
                    'jira_attachment_filename' => $firstAttachment['filename'] ?? null,
                    'jira_attachment_response' => $jiraResponse,
                    'jira_attachment_error' => null,
                ])->save();
            } catch (\Throwable $exception) {
                $attachment->forceFill(['jira_attachment_error' => $exception->getMessage()])->save();
                $this->recordSafely($exception, 'Jira support ticket attachment upload failed');
            }
        }

        return true;
    }

    private function saveStoredAttachments(JiraTicket $ticket, array $storedAttachments): bool
    {
        foreach ($storedAttachments as $attachment) {
            try {
                $ticket->attachments()->create($attachment);
            } catch (\Throwable $exception) {
                $this->recordSafely($exception, 'Jira support ticket attachment database save failed');
            }
        }

        return true;
    }

    private function deleteStoredAttachments(array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (! empty($attachment['attachment_path'])) {
                Storage::disk('local')->delete($attachment['attachment_path']);
            }
        }
    }

    private function runAfterCreateStep(callable $callback, string $context, JiraTicket $ticket): void
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            $this->recordSafely($exception, $context.' Ticket ID: '.$ticket->id);
        }
    }

    private function recordSafely(\Throwable $exception, string $context): void
    {
        try {
            $this->errorLog->record($exception, $context);
        } catch (\Throwable) {
            report($exception);
        }
    }

    private function recordMessageSafely(string $context, string $message, string $file, int $line): void
    {
        try {
            $this->errorLog->recordMessage($context, $message, $file, $line);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
