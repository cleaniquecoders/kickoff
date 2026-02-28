<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface AuthorizedMenuBuilder
 *
 * Contract for menu builders that support authorization.
 */
interface AuthorizedMenuBuilder
{
    /**
     * Check if the current user is authorized to view this menu.
     */
    public function isAuthorized(): bool;

    public function setAuthorization(callable|string|bool $authorization): self;

    /**
     * Get the authorization string for use in Blade directives.
     */
    public function getAuthorizationForBlade(): ?string;
}
