<?php

it('runs on PHP 8.5 or above')
    ->expect(phpversion())
    ->toBeGreaterThanOrEqual('8.5.0');

arch()
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsedIn([
        'app',
        'config',
        'database',
        'routes',
        'support',
    ]);

it('does not using url method')
    ->expect(['url'])
    ->not
    ->toBeUsed();

arch()
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch()
    ->expect('App\Policies')
    ->toBeClasses();

arch()
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');

arch()
    ->expect('App\Mail')
    ->toBeClasses();

arch()
    ->expect('App\Mail')
    ->toExtend('Illuminate\Mail\Mailable');

arch()
    ->expect('env')
    ->toOnlyBeUsedIn([
        'config',
    ]);

// toUse() matches class/function names, never strings like 'DB::table' — the old list of
// 'DB::*' strings could not fail. Forbid the facade itself and allowlist deliberate users.
arch('no raw database access outside the allowlist')
    ->expect('App')
    ->not
    ->toUse('Illuminate\Support\Facades\DB')
    ->ignoring([
        // Bulk retention deletes across tables that have no Eloquent model (audits, telescope).
        'App\Console\Commands\PurgeExpiredDataCommand',
    ]);

arch()
    ->expect('App\Concerns')
    ->toBeTraits();

arch()
    ->expect('App\Enums')
    ->toBeEnums();

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch()
    ->expect('App\Http\Middleware')
    ->toBeClasses();

arch()
    ->expect('dumpSql')
    ->not
    ->toBeUsedIn(['app']);
