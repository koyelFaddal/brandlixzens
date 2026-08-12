<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMessage extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $mailBody,
        private readonly ?string $attachmentPath = null,
        private readonly ?string $attachmentName = null,
        private readonly ?string $attachmentMime = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-message',
            with: ['mailBody' => $this->mailBody],
        );
    }

    public function attachments(): array
    {
        if (! $this->attachmentPath) {
            return [];
        }

        return [
            Attachment::fromPath($this->attachmentPath)
                ->as($this->attachmentName ?: basename($this->attachmentPath))
                ->withMime($this->attachmentMime ?: 'application/octet-stream'),
        ];
    }
}
