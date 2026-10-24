<?php

use App\Livewire\Admin\MailHistory\Index;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(AccessControlSeeder::class));

it('clamps a crafted perPage query param to an allowed page size', function (int $requested, int $expected) {
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');

    Livewire::actingAs($admin)
        ->withQueryParams(['perPage' => $requested])
        ->test(Index::class)
        ->assertSet('perPage', $expected);
})->with([
    'allowed' => [50, 50],
    'unbounded' => [1000000, 10],
    'zero' => [0, 10],
]);
