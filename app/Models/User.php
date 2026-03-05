<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Child::class);
    }

    public function sharedChildren(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_user')->withTimestamps();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Child> */
    public function childrenAccessible(): \Illuminate\Database\Eloquent\Collection
    {
        return Child::accessibleBy($this)->orderBy('created_at')->get();
    }

    public function actions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Action::class);
    }

    /**
     * IDs de los co-padres: usuarios que comparten al menos un hijo con este usuario.
     */
    public function coParentIds(): Collection
    {
        $accessibleChildIds = Child::accessibleBy($this)->pluck('id');

        // Propietarios originales de hijos a los que tengo acceso compartido
        $ownerIds = Child::accessibleBy($this)
            ->where('user_id', '!=', $this->id)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        // Usuarios con acceso compartido a mis hijos
        $sharedUserIds = DB::table('child_user')
            ->whereIn('child_id', $accessibleChildIds)
            ->where('user_id', '!=', $this->id)
            ->pluck('user_id');

        return $ownerIds->merge($sharedUserIds)->unique()->values();
    }
}
