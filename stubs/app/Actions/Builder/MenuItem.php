<?php

declare(strict_types=1);

namespace App\Actions\Builder;

use CleaniqueCoders\Traitify\Contracts\Builder;
use InvalidArgumentException;

/**
 * Class MenuItem
 *
 * Represents a menu item with configurable attributes, visibility, and nested children.
 */
class MenuItem implements Builder
{
    private string $label;

    /**
     * Empty for a parent item, which is a disclosure control rather than a link.
     * Typed and uninitialised, a parent with no URL was a 500 on every page.
     */
    private string $url = '';

    private string $target = '_self';

    private array $attributes = [];

    /** @var array<MenuItem> */
    private array $children = [];

    private string $icon = 'grid-2x2';

    private ?string $description = null;

    private ?string $tooltip = null;

    private string $type = 'link'; // 'link' or 'form'

    /** @var array<string, mixed> */
    private array $formAttributes = []; // Attributes specific to forms

    /** @var callable|bool */
    private $visible = true;

    /** Active only on its exact URL: a hub at /admin must not light up on /admin/roles. */
    private bool $exact = false;

    /** @var array<string, mixed> */
    private array $output = [];

    /**
     * Set the label for the menu item.
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the URL for the menu item.
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the target for the menu item.
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Add an attribute to the menu item.
     */
    public function addAttribute(string $key, string $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Add a child menu item.
     */
    public function addChild(MenuItem $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Set the icon for the menu item.
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the description for the menu item.
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the tooltip for the menu item.
     */
    public function setTooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function setVisible(callable|bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Mark the item active on its exact URL only, not on pages beneath it.
     */
    public function exactMatch(): self
    {
        $this->exact = true;

        return $this;
    }

    /**
     * Set the type of the menu item.
     */
    public function setType(string $type): self
    {
        if (! in_array($type, ['link', 'form'], true)) {
            throw new InvalidArgumentException('The type must be "link" or "form".');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Add form-specific attributes.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function setFormAttributes(array $attributes): self
    {
        $this->formAttributes = $attributes;

        return $this;
    }

    /**
     * Determine if the menu item is visible.
     */
    public function isVisible(): bool
    {
        return is_callable($this->visible) ? call_user_func($this->visible) : $this->visible;
    }

    /**
     * Build and construct the menu item output.
     */
    public function build(): self
    {
        // If the item is not visible, return empty output
        if (! $this->isVisible()) {
            $this->output = [];

            return $this;
        }

        $children = array_values(array_filter(
            array_map(fn (MenuItem $child) => $child->build()->toArray(), $this->children),
            fn (array $child) => $child !== [] // Exclude hidden children
        ));

        // A group with nothing permitted under it and nowhere of its own to go is not drawn.
        if ($this->children !== [] && $children === [] && in_array($this->url, ['', '#'], true)) {
            $this->output = [];

            return $this;
        }

        $this->output = [
            'label' => $this->label,
            'url' => $this->url,
            'active' => $this->isCurrentUrl(),
            'target' => $this->target,
            'attributes' => $this->attributes,
            'icon' => $this->icon,
            'description' => $this->description,
            'tooltip' => $this->tooltip,
            'type' => $this->type,
            'formAttributes' => $this->type === 'form' ? $this->formAttributes : [],
            'children' => $children,
            'visible' => true, // Add visible flag to output for blade template
        ];

        return $this;
    }

    /**
     * Whether this item is where the user currently is.
     *
     * Prefix match, so a detail page belongs to its index. The slash is load-bearing:
     * without it /billing would claim /billing-history. The site root is exact, or it
     * would own every page.
     */
    private function isCurrentUrl(): bool
    {
        $url = rtrim($this->url, '/');

        if ($url === '' || $url === '#') {
            return false;
        }

        $current = request()->url();

        if ($this->exact || $url === rtrim(request()->root(), '/')) {
            return $current === $url;
        }

        return $current === $url || str_starts_with($current, $url.'/');
    }

    /**
     * Get the menu item as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->output;
    }

    /**
     * Get the menu item as JSON.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options, 512);
    }
}
