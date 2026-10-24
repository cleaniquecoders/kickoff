<?php

use App\Livewire\Admin\Settings\Show;
use App\Models\User;
use App\Providers\AppServiceProvider;
use App\Settings\MailSettings;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

it('lets an admin switch mail tracking off without touching .env', function () {
    $this->seed(AccessControlSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');

    Livewire::actingAs($admin)
        ->test(Show::class, ['section' => 'email'])
        ->assertSet('settings.email.track_opens', true)
        ->set('settings.email.track_opens', false)
        ->set('settings.email.track_clicks', false)
        ->call('saveSettings')
        ->assertHasNoErrors();

    $settings = app(MailSettings::class);
    expect($settings->track_opens)->toBeFalse()->and($settings->track_clicks)->toBeFalse();

    // The boot-time overlay is what the mailhistory listener reads.
    (new AppServiceProvider(app()))->boot();

    expect(config('mailhistory.tracking.open.enabled'))->toBeFalse()
        ->and(config('mailhistory.tracking.click.enabled'))->toBeFalse();
});
