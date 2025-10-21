<?php

namespace App\Actions\Builder\Menu;

use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;
use Illuminate\Support\Collection;

class Base implements Builder, Menu
{
    private Collection $menus;

    private ?string $headingLabel = null;

    private ?string $headingIcon = null;

    /** @var callable|string|null */
    private $authorization = null;

    /**
     * Return the list of menus.
     */
    public function menus(): Collection
    {
        return $this->menus;
    }

    /**
     * Set the heading label for the menu.
     */
    public function setHeadingLabel(string $headingLabel): self
    {
        $this->headingLabel = $headingLabel;

        return $this;
    }

    /**
     * Get the heading label for the menu.
     */
    public function getHeadingLabel(): ?string
    {
        return $this->headingLabel;
    }

    /**
     * Set the heading icon for the menu.
     */
    public function setHeadingIcon(string $headingIcon): self
    {
        $this->headingIcon = $headingIcon;

        return $this;
    }

    /**
     * Get the heading icon for the menu.
     */
    public function getHeadingIcon(): ?string
    {
        return $this->headingIcon;
    }

    /**
     * Set the authorization requirement for the menu.
     *
     * @param  callable|string  $authorization  Gate name or callable that returns boolean
     */
    public function setAuthorization($authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * Get the authorization requirement for the menu.
     *
     * @return callable|string|null
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Check if the current user is authorized to view this menu.
     */
    public function isAuthorized(): bool
    {
        if ($this->authorization === null) {
            return true;
        }

        if (is_callable($this->authorization)) {
            return call_user_func($this->authorization);
        }

        if (is_string($this->authorization)) {
            return \Illuminate\Support\Facades\Gate::allows($this->authorization);
        }

        return true;
    }

    /**
     * Get the authorization string for use in Blade directives.
     */
    public function getAuthorizationForBlade(): ?string
    {
        if (is_string($this->authorization)) {
            return $this->authorization;
        }

        return null;
    }

    /**
     * Build the menu items.
     */
    public function build(): self
    {
        return $this;
    }
}
