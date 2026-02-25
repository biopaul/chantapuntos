<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Child;
use App\Models\User;

class ChildPolicy
{
    public function delete(User $user, Child $child): bool
    {
        return $child->user_id === $user->id;
    }

    public function update(User $user, Child $child): bool
    {
        if ($child->user_id === $user->id) {
            return true;
        }
        return $child->users()->where('users.id', $user->id)->exists();
    }
}
