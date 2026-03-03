<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = [
        'inviter_id',
        'email',
        'token',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Aplica esta invitación al usuario: vincula los hijos del inviter y marca la invitación como aceptada.
     * Solo debe llamarse si la invitación es válida (no aceptada, no expirada, email coincide).
     */
    public function acceptFor(User $user): void
    {
        $childIds = $this->inviter->children()->pluck('id');
        foreach ($childIds as $childId) {
            $user->sharedChildren()->syncWithoutDetaching([$childId]);
        }
        $this->update(['accepted_at' => now()]);
    }
}
