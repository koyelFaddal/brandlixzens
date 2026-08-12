<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiJob extends Model
{
    protected $fillable = [
        'job_title', 'category', 'overview', 'work_mode', 'work_location',
        'employment_type', 'responsibilities', 'required_skills',
        'preferred_skills', 'experience_required', 'job_post_date', 'preview_image',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }
}
