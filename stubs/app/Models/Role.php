<?php

declare(strict_types=1);

namespace App\Models;

use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property bool $is_enabled
 */
class Role extends \Spatie\Permission\Models\Role implements Auditable
{
    use AuditingTrait;
    use InteractsWithUuid;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
        'is_enabled',
    ];

    /**
     * Pin fresh instances to the `web` guard.
     *
     * Browser routes authenticate via `auth:sanctum`; once that guard passes,
     * Laravel makes `sanctum` the request's default guard. Spatie would then
     * resolve a guardless fresh instance (e.g. from `Role::withCount('users')`)
     * against `sanctum` (provider is null), and `morphedByMany(null, …)` throws
     * "Class name must be a valid object or a string". All roles here are stored
     * under `web`, so pin it. Hydrated rows still get their real guard from the DB.
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] ??= 'web';

        parent::__construct($attributes);
    }

    /**
     * Roles seeded from config/access-control.php — cannot be deleted or disabled.
     */
    public function isProtected(): bool
    {
        return in_array($this->name, array_keys(config('access-control.roles', [])));
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?? str($attributes['name'] ?? '')->title()->replace('-', ' ')->value(),
        );
    }
}
