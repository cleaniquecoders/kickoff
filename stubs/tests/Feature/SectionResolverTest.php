<?php

use App\Actions\Builder\Menu\SectionResolver;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Http\Request;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('superadmin');
});

test('resolve() returns the authorized sections with heading metadata', function () {
    actingAs($this->admin);

    $sections = collect(SectionResolver::resolve()['sections']);

    expect($sections->pluck('key')->all())->toContain('administration')
        ->and($sections->firstWhere('key', 'administration')['label'])->toBe('Administration')
        ->and($sections->firstWhere('key', 'administration')['landing'])->toBe(route('admin.index'));
});

test('every section exposes a real (non-#) landing URL', function () {
    actingAs($this->admin);

    foreach (SectionResolver::resolve()['sections'] as $section) {
        expect($section['landing'])->not->toBe('#');
    }
});

test('the active section is the one owning the current URL by prefix', function () {
    actingAs($this->admin);

    // A sub-page under Administration that is not itself a menu leaf.
    app()->instance('request', Request::create(route('admin.roles.index').'/42/edit'));

    $active = SectionResolver::resolve()['active'];

    expect($active['key'])->toBe('administration')
        ->and($active['owns'])->toBeTrue();
});

test('with no section owning the URL, the first section is the fallback active', function () {
    actingAs($this->admin);

    app()->instance('request', Request::create('http://localhost/some/unmatched/path'));

    $resolved = SectionResolver::resolve();

    expect($resolved['active'])->not->toBeNull()
        ->and($resolved['active']['key'])->toBe($resolved['sections'][0]['key']);
});
