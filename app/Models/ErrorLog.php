<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'lead_id',
    'file_path',
    'line_number',
    'short_message',
    'full_error_message',
])]
class ErrorLog extends Model
{
}