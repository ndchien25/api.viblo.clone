<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The users that belong to the organ.
     */

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user', 'user_id', 'organ_id')
            ->withPivot('role', 'total_post', 'total_member', 'total_view', 'joined_at');
    }
}
