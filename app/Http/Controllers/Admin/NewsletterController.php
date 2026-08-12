<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMessage;
use App\Models\NewsletterSentMail;
use App\Models\NewsletterSubscription;
use App\Services\ErrorLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(): View
    {
        return view('admin.newsletter.index', [
            'subscribers' => NewsletterSubscription::query()
                ->latest('id')
                ->paginate(25),
        ]);
    }

    public function send(Request $request, NewsletterSubscription $newsletterSubscription): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ], [
            'subject.required' => 'Please enter an email subject.',
            'body.required' => 'Please enter the email body.',
            'attachment.max' => 'The attachment may not be larger than 10 MB.',
        ]);

        $attachmentPath = null;
        $sentMail = null;

        try {
            $attachment = $request->file('attachment');

            if ($attachment) {
                $safeName = Str::uuid().'_'.Str::slug(pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $attachment->getClientOriginalExtension();
                $attachmentPath = Storage::disk('local')->putFileAs(
                    'newsletter-attachments/'.now()->format('Y/m'),
                    $attachment,
                    $safeName.($extension ? '.'.$extension : '')
                );

                if (! $attachmentPath) {
                    throw new \RuntimeException('Unable to store newsletter attachment.');
                }
            }

            $sentMail = NewsletterSentMail::create([
                'newsletter_subscription_id' => $newsletterSubscription->id,
                'recipient_email' => $newsletterSubscription->email,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'attachment_path' => $attachmentPath,
                'sent_by' => $request->user()?->id,
                'sent_at' => now(),
            ]);

            Mail::to($newsletterSubscription->email)->send(new NewsletterMessage(
                mailSubject: $validated['subject'],
                mailBody: $validated['body'],
                attachmentPath: $attachmentPath ? Storage::disk('local')->path($attachmentPath) : null,
                attachmentName: $attachment?->getClientOriginalName(),
                attachmentMime: $attachment?->getMimeType(),
            ));

            return redirect()
                ->route('admin.newsletter.index')
                ->with('status', 'Newsletter email sent successfully to '.$newsletterSubscription->email.'.');
        } catch (\Throwable $exception) {
            $sentMail?->delete();

            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }

            $this->errorLog->record($exception, 'Admin newsletter email failed');

            return back()
                ->withInput()
                ->withErrors(['newsletter' => 'Unable to send the email. Please try again.']);
        }
    }

    public function history(NewsletterSubscription $newsletterSubscription): View
    {
        return view('admin.newsletter.history', [
            'subscriber' => $newsletterSubscription,
            'sentMails' => $newsletterSubscription->sentMails()
                ->latest('sent_at')
                ->paginate(25),
        ]);
    }

    public function showMail(
        NewsletterSubscription $newsletterSubscription,
        NewsletterSentMail $newsletterSentMail
    ): View {
        $this->ensureMailBelongsToSubscriber($newsletterSubscription, $newsletterSentMail);

        return view('admin.newsletter.show', [
            'subscriber' => $newsletterSubscription,
            'sentMail' => $newsletterSentMail,
        ]);
    }

    public function attachment(
        NewsletterSubscription $newsletterSubscription,
        NewsletterSentMail $newsletterSentMail
    ): StreamedResponse {
        $this->ensureMailBelongsToSubscriber($newsletterSubscription, $newsletterSentMail);

        if (! $newsletterSentMail->attachment_path || ! Storage::disk('local')->exists($newsletterSentMail->attachment_path)) {
            abort(404);
        }

        $storedName = basename($newsletterSentMail->attachment_path);
        $downloadName = Str::after($storedName, '_') ?: $storedName;

        return Storage::disk('local')->download($newsletterSentMail->attachment_path, $downloadName);
    }

    private function ensureMailBelongsToSubscriber(
        NewsletterSubscription $newsletterSubscription,
        NewsletterSentMail $newsletterSentMail
    ): void {
        abort_unless(
            $newsletterSentMail->newsletter_subscription_id === $newsletterSubscription->id,
            404
        );
    }
}
