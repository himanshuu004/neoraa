<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'username',
        'password',
        'role_id',
        'profile_image',
    ];

    protected $hidden = [
        'password',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function therapistProfile(): HasOne
    {
        return $this->hasOne(TherapistProfile::class, 'user_id');
    }

    public function traineeProfile(): HasOne
    {
        return $this->hasOne(TraineeProfile::class, 'user_id');
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getRememberTokenName(): ?string
    {
        return null;
    }

    public function roleName(): string
    {
        return strtolower((string) ($this->role?->role_name ?? ''));
    }

    public function isAdmin(): bool
    {
        return $this->roleName() === 'admin';
    }

    public function isTherapist(): bool
    {
        return $this->roleName() === 'therapist';
    }

    public function isCoordinator(): bool
    {
        return $this->roleName() === 'coordinator';
    }

    public function isTrainee(): bool
    {
        return $this->roleName() === 'trainee';
    }

    public function dashboardRouteName(): string
    {
        return match ($this->roleName()) {
            'admin' => 'admin.dashboard',
            'coordinator' => 'coordinator.dashboard',
            'trainee' => 'trainee.dashboard',
            default => 'therapist.dashboard',
        };
    }

    public function displayName(): string
    {
        return $this->therapistProfile?->name
            ?? $this->traineeProfile?->name
            ?? $this->username
            ?? 'User';
    }
}
