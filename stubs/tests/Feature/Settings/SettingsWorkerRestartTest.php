<?php

use App\Livewire\Admin\Settings\Show;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('restarts queue workers after saving settings so queued jobs see the new values', function () {
    $this->seed(AccessControlSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');

    expect(Cache::get('illuminate:queue:restart'))->toBeNull();

    Livewire::actingAs($admin)
        ->test(Show::class, ['section' => 'general'])
        ->set('settings.general.site_name', 'Renamed')
        ->set('settings.general.timezone', 'UTC')
        ->call('saveSettings')
        ->assertHasNoErrors();

    expect(Cache::get('illuminate:queue:restart'))->not->toBeNull();
});
