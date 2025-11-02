<?php

namespace App\Contracts;

/**
 * Interface HeadingMenuBuilder
 *
 * Contract for menu builders that support heading labels and icons.
 */
interface HeadingMenuBuilder
{
    /**
     * Check if the menu has a heading label.
     */
    public function hasHeadingLabel(): bool;

    /**
     * Check if the menu has a heading icon.
     */
    public function hasHeadingIcon(): bool;

    /**
     * Set the heading label for the menu.
     */
    public function setHeadingLabel(string $headingLabel): self;

    /**
     * Get the heading label for the menu.
     */
    public function getHeadingLabel(): ?string;

    /**
     * Set the heading icon for the menu.
     */
    public function setHeadingIcon(string $headingIcon): self;

    /**
     * Get the heading icon for the menu.
     */
    public function getHeadingIcon(): ?string;
}
