<?php

use App\Models\User;

it('keeps credentials out of the audit trail', function () {
    expect((new User)->getAuditExclude())
        ->toContain('password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes');
});

it('keeps 2FA secrets out of serialized users', function () {
    $user = User::factory()->create();
    $user->forceFill(['two_factor_secret' => 'secret', 'two_factor_recovery_codes' => 'codes'])->save();

    expect($user->fresh()->toArray())
        ->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});
