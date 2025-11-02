<?php

namespace App\Actions\Builder\Menu;

use App\Concerns\ProcessesMenuItems;
use App\Contracts\AuthorizedMenuBuilder;
use App\Contracts\HeadingMenuBuilder;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

abstract class Base implements AuthorizedMenuBuilder, Builder, HeadingMenuBuilder, Menu
{
    use ProcessesMenuItems;

    protected Collection $menus;

    protected ?string $headingLabel = null;

    protected ?string $headingIcon = null;

    /** @var callable|string|bool|null */
    protected $authorization = null;

    public function __construct()
    {
        $this->menus = collect();
    }

    /**
     * Return the list of menus.
     */
    public function menus(): Collection
    {
        return $this->menus;
    }

    /**
     * Set the menus collection.
     */
    protected function setMenus(Collection $menus): self
    {
        $this->menus = $menus;

        return $this;
    }

    /**
     * Add menu items to the collection.
     */
    protected function addMenuItems(Collection $menuItems): self
    {
        $this->menus = $this->menus->merge($menuItems);

        return $this;
    }

    public function hasHeadingLabel(): bool
    {
        return ! empty($this->getHeadingLabel());
    }

    public function hasHeadingIcon(): bool
    {
        return ! empty($this->getHeadingIcon());
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
     * @param callable|string|bool $authorization Gate name, callable, or boolean
     */
    public function setAuthorization($authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * Get the authorization requirement for the menu.
     *
     * @return callable|string|bool|null
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
        return match (true) {
            $this->authorization === null => true,
            is_callable($this->authorization) => call_user_func($this->authorization),
            is_string($this->authorization) => Gate::allows($this->authorization),
            is_bool($this->authorization) => $this->authorization,
            default => true,
        };
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
     * This method should be implemented by concrete menu classes.
     */
    abstract public function build(): self;

    /**
     * Get menu configuration for the concrete menu class.
     * This method should return an array of menu item configurations.
     *
     * @return array<callable>
     */
    abstract protected function getMenuConfiguration(): array;
}
