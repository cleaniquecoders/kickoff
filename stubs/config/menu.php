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
| - sections: builders offered one at a time through the section switcher below
|             the globals. Each supplies its label, icon and landing page through
|             setHeadingLabel / setHeadingIcon / setHeadingUrl — e.g. Administration
|             (Identity, Mail, Media Library, Backups, Settings, Developers).
| - footer:   builders pinned to the bottom, below the spacer (e.g. Resources).
|
| Once a user can see two or more sections, the sidebar becomes a rail + panel
| (App\Actions\Builder\Menu\NavRail): sections become rail areas, the logo links
| to the dashboard, other globals sit at the rail foot and the footer becomes Help.
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
