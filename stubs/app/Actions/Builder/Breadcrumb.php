<?php

declare(strict_types=1);

namespace App\Actions\Builder;

use App\Actions\Builder\Menu\Base as MenuBase;
use Illuminate\Support\Facades\Route;

/**
 * Breadcrumb builder.
 *
 * Derives the breadcrumb trail from the SAME menu tree that renders the sidebar
 * (see App\Actions\Builder\Menu\*), so navigation and breadcrumbs can never
 * drift apart. The active page is matched by URL against each menu builder; the
 * matched leaf's ancestor groups (and the menu heading) become the trail, rooted
 * at the dashboard.
 *
 * Every trail item is `['label' => string, 'url' => ?string]`. A `null`/`'#'`
 * url renders as plain text; the LAST item always renders as the current page
 * (no link) regardless of its url — so a leaf used as a parent (via `for()` +
 * `push()`) keeps its link.
 *
 * Usage:
 *   // A page that is itself a menu leaf — fully automatic:
 *   Breadcrumb::current();                         // Dashboard > Administration > Settings > General
 *
 *   // A detail page that is NOT a menu leaf — resolve its parent, append a leaf:
 *   Breadcrumb::for('admin.roles.index')->push(__('Role Details'));
 *
 *   // A hub / non-menu page — give the trail explicitly (dashboard auto-prepended):
 *   Breadcrumb::manual([['label' => __('Administration')]]);
 */
class Breadcrumb
{
    /** @var array<int, array{label: string, url: ?string}> */
    private array $items = [];

    public static function make(): self
    {
        return new self;
    }

    /**
     * Build the trail for the current request URL.
     */
    public static function current(): self
    {
        $breadcrumb = new self;
        $breadcrumb->resolveByUrl(request()->url());

        return $breadcrumb;
    }

    /**
     * Build the trail for a specific route (and params), matched against the menu.
     *
     * @param  array<int|string, mixed>  $params
     */
    public static function for(string $route, array $params = []): self
    {
        $breadcrumb = new self;

        if (Route::has($route)) {
            $breadcrumb->resolveByUrl(route($route, $params));
        }

        return $breadcrumb;
    }

    /**
     * Build an explicit trail. The dashboard root is prepended automatically.
     *
     * @param  array<int, array{label: string, url?: ?string}>  $items
     */
    public static function manual(array $items): self
    {
        $breadcrumb = new self;
        $breadcrumb->items = array_merge(
            $breadcrumb->rootCrumb(),
            array_map(
                fn (array $item): array => ['label' => $item['label'], 'url' => $item['url'] ?? null],
                $items
            )
        );

        return $breadcrumb;
    }

    /**
     * Append a current-page crumb (no link) to the trail.
     */
    public function push(string $label, ?string $url = null): self
    {
        $this->items[] = ['label' => $label, 'url' => $url];

        return $this;
    }

    /**
     * Get the resolved trail.
     *
     * @return array<int, array{label: string, url: ?string}>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Menu builders searched (in order) when resolving the active URL. Derived
     * from config/menu.php so the breadcrumb sources and the sidebar (sections +
     * footer + globals) can never drift. Sections are searched first so a
     * section leaf wins over a same-URL global.
     *
     * @return array<int, string>
     */
    private static function sources(): array
    {
        return array_values(array_unique(array_merge(
            (array) config('menu.sections', ['administration', 'media-management']),
            (array) config('menu.footer', ['sidebar-footer']),
            (array) config('menu.globals', ['sidebar']),
        )));
    }

    /**
     * Resolve the trail by matching $url against each menu builder's tree.
     */
    private function resolveByUrl(string $url): bool
    {
        foreach (self::sources() as $source) {
            $menu = menu($source);

            $path = $this->search($menu->menus()->all(), $url);

            if ($path === null) {
                continue;
            }

            $crumbs = $this->rootCrumb();

            if ($menu instanceof MenuBase && $menu->hasHeadingLabel()) {
                $crumbs[] = ['label' => (string) $menu->getHeadingLabel(), 'url' => $menu->getHeadingUrl()];
            }

            $this->items = array_merge($crumbs, $path);

            return true;
        }

        return false;
    }

    /**
     * Depth-first search for the menu item whose url matches, returning the
     * label/url path from the top group down to the matched leaf.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array{label: string, url: ?string}>  $ancestors
     * @return array<int, array{label: string, url: ?string}>|null
     */
    private function search(array $items, string $url, array $ancestors = []): ?array
    {
        foreach ($items as $item) {
            $itemUrl = $item['url'] ?? null;

            if ($itemUrl === $url && $url !== '#') {
                return array_merge($ancestors, [['label' => (string) $item['label'], 'url' => $itemUrl]]);
            }

            $children = $item['children'] ?? [];

            if (! empty($children)) {
                $branch = array_merge($ancestors, [[
                    'label' => (string) $item['label'],
                    'url' => ($itemUrl !== null && $itemUrl !== '#') ? $itemUrl : null,
                ]]);

                $found = $this->search($children, $url, $branch);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * The dashboard root crumb.
     *
     * @return array<int, array{label: string, url: ?string}>
     */
    private function rootCrumb(): array
    {
        return [[
            'label' => __('Dashboard'),
            'url' => Route::has('dashboard') ? route('dashboard') : null,
        ]];
    }
}
