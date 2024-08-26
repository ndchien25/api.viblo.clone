<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Serie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'user_id',
        'post_ids',
    ];

    protected $casts = [
        'post_ids' => 'array',
    ];
    /**
     * Get the serie that owns the serie.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
