<?php

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

    /**
     * Set the authorization requirement for the menu.
     *
     * @param  callable|string|bool  $authorization  Gate name, callable, or boolean
     */
    public function setAuthorization($authorization): self;

    /**
     * Get the authorization string for use in Blade directives.
     */
    public function getAuthorizationForBlade(): ?string;
}
