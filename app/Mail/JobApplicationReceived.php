<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public JobApplication $application,
        public string $resumeUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Job Application: '.$this->application->job_title_snapshot
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application-received',
            with: [
                'application' => $this->application,
                'resumeUrl' => $this->resumeUrl,
            ],
        );
    }
}
