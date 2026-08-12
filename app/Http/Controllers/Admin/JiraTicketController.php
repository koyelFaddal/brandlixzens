<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JiraTicket;
use App\Models\JiraTicketAttachment;
use App\Services\ErrorLogService;
use App\Services\JiraTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JiraTicketController extends Controller
{
    public function __construct(
        private readonly JiraTicketService $tickets,
        private readonly ErrorLogService $errorLog,
    ) {
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $statuses = $this->statusOptions();
        $selectedStatus = $this->canonicalStatusName($status);

        $tickets = JiraTicket::query()
            ->withCount('attachments')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('jira_ticket_key', 'like', $search.'%')
                        ->orWhere('name', 'like', $search.'%')
                        ->orWhere('email', 'like', $search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%');
                });
            })
            ->when($selectedStatus !== '', function ($query) use ($selectedStatus): void {
                $query->whereRaw('LOWER(status) = ?', [mb_strtolower($selectedStatus)]);
            })
            ->latest('id')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.jira-tickets', [
            'tickets' => $tickets,
            'statuses' => $statuses,
            'search' => $search,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function show(JiraTicket $jiraTicket): JsonResponse
    {
        try {
            $jiraTicket = $this->tickets->syncFromJira($jiraTicket);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Jira support ticket status sync failed while opening admin ticket');
        }

        $jiraTicket = $jiraTicket->fresh()->load('attachments');

        try {
            $transitions = $this->tickets->availableTransitions($jiraTicket);
        } catch (\Throwable $exception) {
            $transitions = [];
            $this->errorLog->record($exception, 'Jira support ticket transition load failed while opening admin ticket');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket loaded successfully.',
            'data' => [
                'id' => $jiraTicket->id,
                'jira_ticket_key' => $jiraTicket->jira_ticket_key,
                'jira_issue_id' => $jiraTicket->jira_issue_id,
                'name' => $jiraTicket->name,
                'email' => $jiraTicket->email,
                'subject' => $jiraTicket->subject,
                'issue_category' => $jiraTicket->issue_category,
                'priority' => $jiraTicket->priority,
                'message' => $jiraTicket->message,
                'status' => $jiraTicket->status,
                'created_at' => optional($jiraTicket->created_at)->toDateTimeString(),
                'jira_response' => $jiraTicket->jira_response,
                'attachments' => $this->formatAttachments($jiraTicket),
                'available_transitions' => $transitions,
            ],
        ]);
    }

    public function updateStatus(Request $request, JiraTicket $jiraTicket): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:120'],
        ]);

        $jiraTicket = $this->tickets->syncFromJira($jiraTicket);

        if (mb_strtolower($validated['status']) === mb_strtolower((string) $jiraTicket->status)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket status is already '.$jiraTicket->status.'.',
                'data' => ['status' => $jiraTicket->status],
            ]);
        }

        try {
            $ticket = $this->tickets->updateStatus($jiraTicket, $validated['status']);
            $transitions = $this->tickets->availableTransitions($ticket);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket status updated successfully.',
                'data' => [
                    'id' => $ticket->id,
                    'status' => $ticket->status,
                    'available_transitions' => $transitions,
                ],
            ]);
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Jira support ticket status update failed');

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    public function attachment(JiraTicketAttachment $attachment)
    {
        if (! Storage::disk('local')->exists($attachment->attachment_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $attachment->attachment_path,
            $attachment->attachment_name,
            ['Content-Type' => $attachment->attachment_type ?: 'application/octet-stream']
        );
    }

    private function formatAttachments(JiraTicket $ticket): array
    {
        return $ticket->attachments->map(fn (JiraTicketAttachment $attachment): array => [
            'id' => $attachment->id,
            'name' => $attachment->attachment_name,
            'type' => $attachment->attachment_type,
            'size' => $attachment->attachment_size,
            'download_url' => route('admin.jira-tickets.attachments.download', ['attachment' => $attachment->id]),
            'jira_attachment_id' => $attachment->jira_attachment_id,
            'jira_attachment_error' => $attachment->jira_attachment_error,
        ])->all();
    }

    private function statusOptions(): array
    {
        $statuses = array_merge(
            JiraTicket::statuses(),
            JiraTicket::query()
                ->whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->filter()
                ->all()
        );

        $uniqueStatuses = [];

        foreach ($statuses as $status) {
            $canonicalStatus = $this->canonicalStatusName((string) $status);

            if ($canonicalStatus === '') {
                continue;
            }

            $uniqueStatuses[mb_strtolower($canonicalStatus)] = $canonicalStatus;
        }

        return array_values($uniqueStatuses);
    }

    private function canonicalStatusName(string $status): string
    {
        $status = trim($status);

        return match (mb_strtolower($status)) {
            'open' => JiraTicket::STATUS_OPEN,
            'in progress' => JiraTicket::STATUS_IN_PROGRESS,
            'done' => JiraTicket::STATUS_DONE,
            'reopened' => JiraTicket::STATUS_REOPENED,
            default => $status,
        };
    }
}
