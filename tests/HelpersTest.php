<?php

use Symfony\Component\Console\Output\BufferedOutput;

test('step shows success indicator', function () {
    $output = new BufferedOutput;
    step('Test step', function () {
        // no-op
    }, $output);

    $result = $output->fetch();
    expect($result)->toContain('Test step')
        ->and($result)->toContain('✅');
});

test('step shows failure indicator and rethrows when critical', function () {
    $output = new BufferedOutput;

    step('Failing step', function () {
        throw new RuntimeException('Something broke');
    }, $output, false, true);
})->throws(RuntimeException::class, 'Something broke');

test('step shows failure but continues when non-critical', function () {
    $output = new BufferedOutput;

    // Should NOT throw
    step('Non-critical step', function () {
        throw new RuntimeException('Minor failure');
    }, $output, false, false);

    $result = $output->fetch();
    expect($result)->toContain('❌')
        ->and($result)->toContain('Minor failure');
});

test('step verbose shows trace on failure', function () {
    $output = new BufferedOutput;

    step('Verbose failing step', function () {
        throw new RuntimeException('Trace test');
    }, $output, true, false);

    expect($output->fetch())->toContain('Trace:');
});

test('run command succeeds', function () {
    // Should not throw
    runCommand('echo hello');
    expect(true)->toBeTrue();
});

test('run command throws on failure', function () {
    runCommand('exit 1');
})->throws(RuntimeException::class, 'Command failed');

test('run command verbose uses passthru', function () {
    // Should not throw - just verifying it works in verbose mode
    ob_start();
    runCommand('echo verbose-test', true);
    $output = ob_get_clean();
    expect($output)->toContain('verbose-test');
});

test('ensure dir creates directory', function () {
    $dir = sys_get_temp_dir().'/kickoff-test-'.uniqid();
    expect($dir)->not->toBeDirectory();

    ensureDir($dir);
    expect($dir)->toBeDirectory();

    rmdir($dir);
});

test('ensure dir noop if exists', function () {
    $dir = sys_get_temp_dir();
    // Should not throw or change anything
    ensureDir($dir);
    expect($dir)->toBeDirectory();
});

test('put file writes content', function () {
    $file = sys_get_temp_dir().'/kickoff-test-'.uniqid().'.txt';
    putFile($file, 'hello world');

    expect($file)->toBeFile()
        ->and(file_get_contents($file))->toBe('hello world');

    unlink($file);
});

test('copy recursively copies directory tree', function () {
    $src = sys_get_temp_dir().'/kickoff-src-'.uniqid();
    $dst = sys_get_temp_dir().'/kickoff-dst-'.uniqid();

    // Create source tree
    mkdir($src.'/subdir', 0755, true);
    file_put_contents($src.'/file.txt', 'root file');
    file_put_contents($src.'/subdir/nested.txt', 'nested file');

    copyRecursively($src, $dst);

    expect($dst.'/file.txt')->toBeFile()
        ->and($dst.'/subdir/nested.txt')->toBeFile()
        ->and(file_get_contents($dst.'/file.txt'))->toBe('root file')
        ->and(file_get_contents($dst.'/subdir/nested.txt'))->toBe('nested file');

    // Cleanup
    unlink($dst.'/subdir/nested.txt');
    unlink($dst.'/file.txt');
    rmdir($dst.'/subdir');
    rmdir($dst);
    unlink($src.'/subdir/nested.txt');
    unlink($src.'/file.txt');
    rmdir($src.'/subdir');
    rmdir($src);
});
