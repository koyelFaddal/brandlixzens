<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'email',
    ];

    public function sentMails(): HasMany
    {
        return $this->hasMany(NewsletterSentMail::class);
    }
}
