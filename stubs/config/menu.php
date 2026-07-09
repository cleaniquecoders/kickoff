<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Sidebar menu structure
|--------------------------------------------------------------------------
|
| Single source of truth for how the sidebar is composed. Each value is a
| menu-builder key resolved by App\Actions\Builder\Menu (see support/menu.php).
|
| - globals:  builders pinned at the top, always visible (no section heading),
|             e.g. Dashboard and Notifications.
| - sections: builders rendered as collapsible groups below the globals. Each one
|             renders its own heading (setHeadingLabel / setHeadingIcon) as an
|             expandable group with its items nested underneath — e.g.
|             Administration (Identity, Mail, Media Library, Backups, Settings,
|             Developers). A builder with no heading renders as a flat list.
| - footer:   builders pinned to the bottom, below the spacer (e.g. Resources).
|
| The breadcrumb builder (App\Actions\Builder\Breadcrumb) reads this SAME list,
| so the sidebar and breadcrumbs can never drift. Add a section by creating its
| menu builder in app/Actions/Builder/Menu/ and appending its key to `sections`.
|
*/

return [
    'globals' => [
        'sidebar',
    ],

    'sections' => [
        'administration',
    ],

    'footer' => [
        'sidebar-footer',
    ],
];
