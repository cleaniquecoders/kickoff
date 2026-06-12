<?php

declare(strict_types=1);

namespace App\Enums;

use CleaniqueCoders\Traitify\Concerns\InteractsWithEnum;
use CleaniqueCoders\Traitify\Contracts\Enum as Contract;

enum UserStatus: string implements Contract
{
    use InteractsWithEnum;

    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case UNVERIFIED = 'unverified';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::SUSPENDED => __('Suspended'),
            self::UNVERIFIED => __('Unverified'),
            self::DELETED => __('Deleted'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => __('Account is active and can sign in.'),
            self::SUSPENDED => __('Account is visible but sign in is blocked.'),
            self::UNVERIFIED => __('Account has not verified its email address.'),
            self::DELETED => __('Account has been removed and can be restored.'),
        };
    }

    /**
     * Badge color for flux:badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::SUSPENDED => 'amber',
            self::UNVERIFIED => 'zinc',
            self::DELETED => 'red',
        };
    }
}
