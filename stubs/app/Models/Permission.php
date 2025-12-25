<?php

namespace App\Models;

use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends \Spatie\Permission\Models\Permission implements Auditable
{
    use AuditingTrait;
    use InteractsWithUuid;

    /**
     * Additional attributes for the permission model.
     */
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
     * Get the display name attribute.
     * Uses display_name if set, otherwise formats the name.
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?? str($attributes['name'] ?? '')->title()->replace('.', ' → ')->replace('-', ' ')->value(),
        );
    }
}
