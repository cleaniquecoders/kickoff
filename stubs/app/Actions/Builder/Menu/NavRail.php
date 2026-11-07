<?php

declare(strict_types=1);

namespace App\Actions\Builder\Menu;

/**
 * The sidebar, resolved for one request.
 *
 * The rail takes over once a user can see MIN_AREAS sections; below that the plain
 * sidebar is the better fit, because a rail with one icon is only wasted width.
 *
 * Nothing here decides access. The builders have already dropped every page the
 * user may not open, and routes keep their own gates: hiding a row is presentation,
 * never authorization.
 */
class NavRail
{
    public const MIN_AREAS = 2;

    /**
     * @return array{
     *     rail: bool,
     *     areas: array<int, array<string, mixed>>,
     *     current: array<string, mixed>|null,
     *     globals: array<int, array<string, mixed>>,
     *     help: array<int, array<string, mixed>>,
     *     pages: array<int, array<string, mixed>>
     * }
     */
    public static function resolve(): array
    {
        $sections = SectionResolver::resolve()['sections'];
        $owner = collect($sections)->where('matchLen', '>', 0)->sortByDesc('matchLen')->first();

        $areas = array_map(fn (array $section): array => [
            'key' => $section['key'],
            'label' => $section['railLabel'],
            'heading' => $section['label'],
            'icon' => $section['icon'],
            'landing' => $section['landing'],
            'current' => $owner !== null && $owner['key'] === $section['key'],
            'items' => $section['items']->all(),
            'hasPanel' => $section['items']->count() > 1,
        ], $sections);

        $globals = self::leaves(self::items('menu.globals'));
        $help = self::leaves(self::items('menu.footer'));

        return [
            'rail' => count($areas) >= self::MIN_AREAS,
            'areas' => $areas,
            'current' => collect($areas)->firstWhere('current', true),
            // The logo is the way home, so the dashboard needs no row of its own.
            'globals' => array_values(array_filter($globals, fn (array $page): bool => $page['url'] !== route('dashboard'))),
            'help' => $help,
            'pages' => self::pages($areas, $globals, $help),
        ];
    }

    /**
     * Every linkable page under the given items, with the group it sits in.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function leaves(array $items, ?string $group = null): array
    {
        $leaves = [];

        foreach ($items as $item) {
            if (! empty($item['children'])) {
                $leaves = array_merge($leaves, self::leaves($item['children'], (string) $item['label']));

                continue;
            }

            if (! in_array($item['url'] ?? '', ['', '#'], true)) {
                $leaves[] = ['group' => $group] + $item;
            }
        }

        return $leaves;
    }

    /**
     * Built items of every builder listed under a config key.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function items(string $configKey): array
    {
        $items = [];

        foreach ((array) config($configKey, []) as $key) {
            $menu = menu($key);

            if ($menu->isAuthorized()) {
                $items = array_merge($items, $menu->menus()->all());
            }
        }

        return $items;
    }

    /**
     * The Cmd-K list: every page this user may open, in both sidebars.
     *
     * @param  array<int, array<string, mixed>>  $areas
     * @param  array<int, array<string, mixed>>  $globals
     * @param  array<int, array<string, mixed>>  $help
     * @return array<int, array<string, mixed>>
     */
    private static function pages(array $areas, array $globals, array $help): array
    {
        $page = fn (array $leaf, string $context): array => [
            'label' => $leaf['label'],
            'url' => $leaf['url'],
            'context' => $context,
            'external' => ($leaf['target'] ?? '_self') === '_blank',
        ];

        $pages = array_map(fn (array $leaf): array => $page($leaf, ''), $globals);

        foreach ($areas as $area) {
            foreach (self::leaves($area['items']) as $leaf) {
                $pages[] = $page($leaf, $area['heading'].($leaf['group'] ? ' › '.$leaf['group'] : ''));
            }
        }

        foreach ($help as $leaf) {
            $pages[] = $page($leaf, __('Help'));
        }

        return collect($pages)->unique('url')->values()->all();
    }
}
