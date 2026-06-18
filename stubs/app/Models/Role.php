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
