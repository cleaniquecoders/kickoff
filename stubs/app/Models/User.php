<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use CleaniqueCoders\Traitify\Concerns\InteractsWithResourceRoute;
use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Yadahan\AuthenticationLog\AuthenticationLogable;

#[Fillable(['name', 'email', 'password', 'uuid'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements AuditableContract, HasMedia, MustVerifyEmail
{
    use AuditableTrait;
    use AuthenticationLogable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Impersonate;
    use InteractsWithMedia;
    use InteractsWithResourceRoute;
    use InteractsWithUuid;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    public function canImpersonate(): bool
    {
        return config('impersonate.enabled');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('superadmin') && ! $this->isSuspended();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function suspend(): void
    {
        $this->forceFill(['suspended_at' => now()])->save();
    }

    public function unsuspend(): void
    {
        $this->forceFill(['suspended_at' => null])->save();
    }

    public function status(): UserStatus
    {
        return match (true) {
            $this->trashed() => UserStatus::DELETED,
            $this->isSuspended() => UserStatus::SUSPENDED,
            $this->email_verified_at === null => UserStatus::UNVERIFIED,
            default => UserStatus::ACTIVE,
        };
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->whereNotNull('suspended_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('suspended_at')->whereNotNull('email_verified_at');
    }

    public function hasNotifications(): bool
    {
        return $this->notifications()->unread()->exists();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
