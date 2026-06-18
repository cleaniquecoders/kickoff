<?php

declare(strict_types=1);

namespace App\Models;

use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends \Spatie\Permission\Models\Permission implements Auditable
{
    use AuditingTrait;
    use InteractsWithUuid;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
        'module',
        'function',
        'is_enabled',
    ];

    /**
     * Pin fresh instances to the `web` guard.
     *
     * Browser routes authenticate via `auth:sanctum`; once that guard passes,
     * Laravel makes `sanctum` the request's default guard. Spatie would then
     * resolve a guardless fresh instance against `sanctum` (provider is null),
     * and relation building (`morphedByMany(null, …)`) throws "Class name must
     * be a valid object or a string". All roles/permissions here are stored under
     * `web`, so pin it. Hydrated rows still get their real guard from the DB.
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] ??= 'web';

        parent::__construct($attributes);
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?? str($attributes['name'] ?? '')->title()->replace('.', ' → ')->replace('-', ' ')->value(),
        );
    }
}
