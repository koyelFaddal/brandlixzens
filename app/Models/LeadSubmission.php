<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSubmission extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'city',
        'phone',
        'email',
        'message',
        'source_url',
        'ip_address',
        'user_agent',
    ];
}
