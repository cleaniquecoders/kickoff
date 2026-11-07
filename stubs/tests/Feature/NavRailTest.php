<?php

use App\Actions\Builder\Menu\NavRail;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Http\Request;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('superadmin');
});

it('keeps the plain sidebar with a single section', function () {
    actingAs($this->admin)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('data-nav-area', false)
        ->assertSee('id="nav-palette-pages"', false);
});

it('switches to the rail at two sections', function () {
    config(['menu.sections' => ['administration', 'sidebar-footer']]);

    actingAs($this->admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('data-nav-area="administration"', false)
        ->assertSee('data-nav-area="sidebar-footer"', false);
});

it('counts only the sections this user can see', function () {
    config(['menu.sections' => ['administration', 'sidebar-footer']]);

    actingAs(User::factory()->create());

    expect(NavRail::resolve()['rail'])->toBeFalse();
});

it('marks the area that owns the current page', function () {
    config(['menu.sections' => ['administration', 'sidebar-footer']]);
    actingAs($this->admin);
    app()->instance('request', Request::create(route('admin.index')));

    expect(NavRail::resolve()['current']['key'])->toBe('administration');
});

it('keeps the dashboard out of the rail globals but in the palette', function () {
    actingAs($this->admin);

    $nav = NavRail::resolve();

    expect(array_column($nav['globals'], 'url'))->not->toContain(route('dashboard'))
        ->and(array_column($nav['pages'], 'url'))->toContain(route('dashboard'));
});

it('lists no page in the palette twice', function () {
    actingAs($this->admin);

    $urls = array_column(NavRail::resolve()['pages'], 'url');

    expect($urls)->toBe(array_values(array_unique($urls)));
});
