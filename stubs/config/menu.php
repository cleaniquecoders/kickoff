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
| - sections: builders offered in the section switcher. The sidebar shows ONE
|             section at a time; picking one from the dropdown navigates to its
|             landing page. A section derives its label, icon and landing URL
|             from the builder's heading (setHeadingLabel / setHeadingIcon /
|             setHeadingUrl), falling back to the builder's first item URL.
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
        'media-management',
    ],

    'footer' => [
        'sidebar-footer',
    ],
];
