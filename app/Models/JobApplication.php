<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'application_type',
        'job_title_snapshot',
        'full_name',
        'email',
        'phone_number',
        'city',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'major_experience',
        'years_of_experience',
        'present_salary',
        'expected_salary',
        'notice_period',
        'current_role',
        'skills',
        'ai_challenge',
        'resume_file_name',
        'resume_file_path',
        'resume_file_size',
        'resume_mime_type',
        'email_notification_sent_at',
        'email_notification_error',
    ];

    protected $casts = [
        'email_notification_sent_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(AiJob::class, 'job_id');
    }
}
