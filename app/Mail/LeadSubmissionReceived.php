<?php

namespace App\Mail;

use App\Models\LeadSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadSubmissionReceived extends Mailable
{
    use SerializesModels;

    public function __construct(public LeadSubmission $leadSubmission)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New website enquiry from '.$this->leadSubmission->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-submission-received');
    }
}
