<?php

namespace App\Services;

use App\Models\JiraTicket;
use App\Models\JiraTicketAttachment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class JiraService
{
    public function createTicket(array $payload): array
    {
        $requestTypeId = $this->requestTypeIdFor($payload['issue_category']);

        $response = $this->client()
            ->withHeaders(['X-ExperimentalApi' => 'opt-in'])
            ->post('/rest/servicedeskapi/request', [
                'serviceDeskId' => (string) config('services.jira.service_desk_id'),
                'requestTypeId' => (string) $requestTypeId,
                'requestFieldValues' => [
                    'summary' => $payload['subject'],
                    'description' => $this->descriptionFor($payload),
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Jira ticket creation failed: '.$response->body());
        }

        return $response->json();
    }

    public function updateTicketStatus(JiraTicket $ticket, string $status): array
    {
        if (! $ticket->jira_ticket_key) {
            throw new RuntimeException('Cannot update Jira because this ticket does not have a Jira key.');
        }

        $transition = $this->transitionFor($ticket->jira_ticket_key, $status);
        $transitionId = (string) $transition['id'];

        Log::info('Jira transition selected', [
            'ticket_id' => $ticket->id,
            'issue_key' => $ticket->jira_ticket_key,
            'current_local_status' => $ticket->status,
            'target_status' => $status,
            'transition' => $transition,
        ]);

        $response = $this->client()
            ->post("/rest/api/3/issue/{$ticket->jira_ticket_key}/transitions", [
                'transition' => ['id' => $transitionId],
            ]);

        if ($response->failed()) {
            Log::error('Jira transition failed', [
                'ticket_id' => $ticket->id,
                'issue_key' => $ticket->jira_ticket_key,
                'target_status' => $status,
                'transition_id' => $transitionId,
                'response' => $response->body(),
            ]);
            throw new RuntimeException('Jira status update failed: '.$response->body());
        }

        $issue = $this->issue($ticket->jira_ticket_key);

        return [
            'transition_id' => $transitionId,
            'selected_transition' => $transition,
            'selected_status' => $status,
            'jira_status' => $this->statusNameFromIssue($issue),
            'jira_response' => $response->json(),
        ];
    }

    public function availableTransitions(string $issueKey): array
    {
        $response = $this->client()->get("/rest/api/3/issue/{$issueKey}/transitions");

        if ($response->failed()) {
            throw new RuntimeException('Unable to read Jira transitions: '.$response->body());
        }

        $transitions = $response->json('transitions', []);

        Log::info('Jira transitions loaded', [
            'issue_key' => $issueKey,
            'transitions' => collect($transitions)->map(fn (array $transition): array => [
                'id' => Arr::get($transition, 'id'),
                'name' => Arr::get($transition, 'name'),
                'to' => Arr::get($transition, 'to.name'),
            ])->all(),
        ]);

        $availableTransitions = [];

        foreach ($transitions as $transition) {
            $toStatus = trim((string) Arr::get($transition, 'to.name'));

            if ($toStatus === '') {
                continue;
            }

            $statusKey = mb_strtolower($toStatus);

            if (isset($availableTransitions[$statusKey])) {
                continue;
            }

            $availableTransitions[$statusKey] = [
                'id' => (string) Arr::get($transition, 'id'),
                'name' => (string) Arr::get($transition, 'name'),
                'to_status' => $toStatus,
            ];
        }

        Log::info('Jira transitions normalized', [
            'issue_key' => $issueKey,
            'available_transitions' => array_values($availableTransitions),
        ]);

        return array_values($availableTransitions);
    }

    public function issueStatus(string $issueKey): string
    {
        return $this->statusNameFromIssue($this->issue($issueKey));
    }

    public function uploadAttachment(JiraTicketAttachment $attachment): array
    {
        $ticket = $attachment->ticket;

        if (! $ticket?->jira_ticket_key) {
            throw new RuntimeException('Cannot upload attachment to Jira because this ticket does not have a Jira key.');
        }

        if (! Storage::disk('local')->exists($attachment->attachment_path)) {
            throw new RuntimeException('Cannot upload attachment to Jira because the local file is missing.');
        }

        $response = $this->client(false)
            ->withHeaders(['X-Atlassian-Token' => 'no-check'])
            ->attach(
                'file',
                fopen(Storage::disk('local')->path($attachment->attachment_path), 'r'),
                $attachment->attachment_name
            )
            ->post("/rest/api/3/issue/{$ticket->jira_ticket_key}/attachments");

        if ($response->failed()) {
            throw new RuntimeException('Jira attachment upload failed: '.$response->body());
        }

        return $response->json();
    }

    public function addAttachmentReferenceComment(JiraTicket $ticket, array $attachmentUrls): void
    {
        if (! $ticket->jira_ticket_key || empty($attachmentUrls)) {
            return;
        }

        $lines = ["Support form attachments are available from the local system:"];

        foreach ($attachmentUrls as $attachment) {
            $lines[] = '- '.$attachment['name'].': '.$attachment['url'];
        }

        $response = $this->client()->post("/rest/api/3/issue/{$ticket->jira_ticket_key}/comment", [
            'body' => [
                'type' => 'doc',
                'version' => 1,
                'content' => [[
                    'type' => 'paragraph',
                    'content' => [[
                        'type' => 'text',
                        'text' => implode("\n", $lines),
                    ]],
                ]],
            ],
        ]);

        if ($response->failed()) {
            Log::warning('Unable to add Jira attachment reference comment', [
                'ticket_id' => $ticket->id,
                'issue_key' => $ticket->jira_ticket_key,
                'response' => $response->body(),
            ]);
        }
    }

    private function transitionFor(string $issueKey, string $status): array
    {
        $transitions = $this->availableTransitions($issueKey);

        foreach ($transitions as $transition) {
            $transitionName = mb_strtolower(trim((string) Arr::get($transition, 'name')));
            $toName = mb_strtolower(trim((string) Arr::get($transition, 'to_status')));
            $candidate = mb_strtolower(trim($status));

            if ($transitionName === $candidate || $toName === $candidate) {
                return $transition;
            }
        }

        throw new RuntimeException("No Jira transition is available from {$issueKey} to {$status}.");
    }

    private function requestTypeIdFor(string $category): string
    {
        $key = strtolower(trim($category));
        $map = config('services.jira.request_type_ids', []);

        if (! empty($map[$key])) {
            return (string) $map[$key];
        }

        if (! empty($map['default'])) {
            return (string) $map['default'];
        }

        throw new RuntimeException('No Jira request type ID is configured for category: '.$category);
    }

    private function descriptionFor(array $payload): string
    {
        $description = [
            $payload['message'],
            '---',
            'Requester: '.$payload['name'].' <'.$payload['email'].'>',
            'Category: '.$payload['issue_category'],
            'Priority: '.$payload['priority'],
            'Source: DGL website support form',
        ];

        if (! empty($payload['attachments'])) {
            $description[] = 'Attachments: '.collect($payload['attachments'])->pluck('attachment_name')->implode(', ');
        }

        return implode("\n\n", $description);
    }

    private function issue(string $issueKey): array
    {
        $response = $this->client()->get("/rest/api/3/issue/{$issueKey}", [
            'fields' => 'status',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Unable to read Jira issue status: '.$response->body());
        }

        Log::info('Jira issue status loaded', [
            'issue_key' => $issueKey,
            'status' => $response->json('fields.status.name'),
        ]);

        return $response->json();
    }

    private function statusNameFromIssue(array $issue): string
    {
        return (string) Arr::get($issue, 'fields.status.name', '');
    }

    private function client(bool $json = true): PendingRequest
    {
        $baseUrl = rtrim((string) config('services.jira.url'), '/');
        $email = (string) config('services.jira.email');
        $token = (string) config('services.jira.api_token');

        if ($baseUrl === '' || $email === '' || $token === '') {
            throw new RuntimeException('Jira credentials are not configured.');
        }

        // return Http::baseUrl($baseUrl)
        //     ->acceptJson()
        //     ->asJson()
        //     ->withBasicAuth($email, $token)
        //     ->timeout(20);

        $request = Http::baseUrl($baseUrl)
            ->acceptJson()
            ->withBasicAuth($email, $token)
            ->withoutVerifying() // TEMPORARY FOR LOCAL TESTING
            ->timeout(20);

        return $json ? $request->asJson() : $request;
    }
}
