<?php

use App\Livewire\Settings\McpTokens;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('mcp tokens page is forbidden without the mcp ability', function () {
    Gate::define('mcp-kit.view-tasks', fn () => false);
    Gate::define('mcp-kit.manage-tasks', fn () => false);

    $this->actingAs(User::factory()->create());

    $this->get(route('settings.mcp-tokens.show'))->assertForbidden();
});

test('an authorized user can view the mcp tokens page', function () {
    Gate::define('mcp-kit.view-tasks', fn () => true);

    $this->actingAs(User::factory()->create());

    $this->get(route('settings.mcp-tokens.show'))->assertOk();
});

test('an authorized user can generate a personal access token', function () {
    Gate::define('mcp-kit.view-tasks', fn () => true);

    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(McpTokens::class)
        ->set('tokenName', 'claude-code-laptop')
        ->call('createToken')
        ->assertHasNoErrors()
        ->assertSet('tokenName', '');

    expect($user->fresh()->tokens()->where('name', 'claude-code-laptop')->exists())->toBeTrue();
});

test('token name is required', function () {
    Gate::define('mcp-kit.view-tasks', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(McpTokens::class)
        ->set('tokenName', '')
        ->call('createToken')
        ->assertHasErrors(['tokenName' => 'required']);
});
