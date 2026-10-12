<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'raw_text',
        'category',
        'priority',
        'category_confidence',
        'priority_confidence',
        'suggested_reply',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUrgentFirst(Builder $query): Builder
    {
        return $query->orderByRaw(
            "FIELD(priority, 'urgent', 'high', 'medium', 'low')"
        );
    }

    public function needsHumanReview(): bool
    {
        return ($this->category_confidence ?? 1) < 0.6;
    }
}
