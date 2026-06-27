<?php

use App\Actions\Builder\Breadcrumb;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('superadmin');
});

function labels(Breadcrumb $breadcrumb): array
{
    return array_map(fn (array $crumb) => $crumb['label'], $breadcrumb->items());
}

test('manual() prepends the Dashboard root', function () {
    $crumbs = Breadcrumb::manual([['label' => 'Administration']]);

    expect(labels($crumbs))->toBe(['Dashboard', 'Administration']);
});

test('a menu leaf resolves the full trail from the menu tree', function () {
    actingAs($this->admin);

    $crumbs = Breadcrumb::for('admin.settings.show', ['section' => 'general']);

    expect(labels($crumbs))->toBe(['Dashboard', 'Administration', 'Settings', 'General']);
});

test('group ancestors are not linked but the heading and root are', function () {
    actingAs($this->admin);

    $items = Breadcrumb::for('admin.settings.show', ['section' => 'general'])->items();

    expect($items[0]['url'])->not->toBeNull()              // Dashboard -> linked
        ->and($items[1]['url'])->not->toBeNull()           // Administration heading -> linked
        ->and($items[2]['url'])->toBeNull();               // Settings group (#) -> plain text
});

test('a detail page keeps its parent leaf linked and the appended leaf plain', function () {
    actingAs($this->admin);

    $crumbs = Breadcrumb::for('admin.roles.index')->push('Role Details');
    $items = $crumbs->items();

    expect(labels($crumbs))->toBe(['Dashboard', 'Administration', 'Identity', 'Roles', 'Role Details'])
        ->and($items[3]['url'])->not->toBeNull()           // Roles (parent) -> linked
        ->and($items[4]['url'])->toBeNull();               // Role Details (current) -> plain text
});

test('an unknown route resolves to an empty trail', function () {
    actingAs($this->admin);

    expect(Breadcrumb::for('non.existent.route')->isEmpty())->toBeTrue();
});
