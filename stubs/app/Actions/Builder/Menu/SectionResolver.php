<?php

declare(strict_types=1);

namespace App\Actions\Builder\Menu;

use Illuminate\Support\Collection;

/**
 * Resolves the sidebar "sections" for the section switcher.
 *
 * Reads the ordered section builder keys from config('menu.sections'), builds
 * each one, drops any the current user can't see (unauthorized or empty), and
 * returns the visible sections plus the one that owns the current page.
 *
 * Each section's label/icon/landing come from the builder's heading
 * (getHeadingLabel/Icon/Url), so there is no duplicated metadata — the builder
 * is the single source of truth. Landing falls back to the first real leaf URL.
 *
 * "Active" is chosen by LONGEST URL-prefix match, so a sub-page such as
 * /admin/roles/42/edit keeps its parent section selected even though no menu
 * item matches that exact URL.
 */
class SectionResolver
{
    /**
     * @return array{
     *     sections: array<int, array{key: string, label: string, icon: string, items: Collection, landing: string, matchLen: int, owns: bool}>,
     *     active: array{key: string, label: string, icon: string, items: Collection, landing: string, matchLen: int, owns: bool}|null
     * }
     */
    public static function resolve(): array
    {
        $currentUrl = request()->url();
        $sections = [];

        foreach ((array) config('menu.sections', []) as $key) {
            $menu = menu($key);

            if (! $menu->isAuthorized()) {
                continue;
            }

            $items = $menu->menus();

            if ($items->isEmpty()) {
                continue;
            }

            $matchLen = 0;

            foreach (self::leafUrls($items->all()) as $url) {
                if ($currentUrl === $url || str_starts_with($currentUrl, rtrim($url, '/').'/')) {
                    $matchLen = max($matchLen, strlen($url));
                }
            }

            $sections[] = [
                'key' => $key,
                'label' => $menu->getHeadingLabel() ?? $key,
                'icon' => $menu->getHeadingIcon() ?? 'circle',
                'items' => $items,
                'landing' => $menu->getHeadingUrl() ?? self::firstLeafUrl($items->all()) ?? '#',
                'matchLen' => $matchLen,
                'owns' => $matchLen > 0,
            ];
        }

        $active = collect($sections)->where('matchLen', '>', 0)->sortByDesc('matchLen')->first()
            ?? ($sections[0] ?? null);

        return ['sections' => $sections, 'active' => $active];
    }

    /**
     * First real (non-'#') leaf URL, recursing into sub-groups. Used as the
     * section landing when the builder sets no explicit heading URL.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private static function firstLeafUrl(array $items): ?string
    {
        foreach ($items as $item) {
            $children = $item['children'] ?? [];

            if (! empty($children)) {
                if (($url = self::firstLeafUrl($children)) !== null) {
                    return $url;
                }

                continue;
            }

            $url = $item['url'] ?? null;

            if (! empty($url) && $url !== '#') {
                return $url;
            }
        }

        return null;
    }

    /**
     * Every real (non-'#') leaf URL under a section, for prefix-based active
     * detection.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, string>
     */
    private static function leafUrls(array $items): array
    {
        $urls = [];

        foreach ($items as $item) {
            $children = $item['children'] ?? [];

            if (! empty($children)) {
                $urls = array_merge($urls, self::leafUrls($children));
            }

            $url = $item['url'] ?? null;

            if (! empty($url) && $url !== '#') {
                $urls[] = $url;
            }
        }

        return $urls;
    }
}
