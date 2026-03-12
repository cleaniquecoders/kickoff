<?php

namespace CleaniqueCoders\Kickoff\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class HelpersTest extends TestCase
{
    public function test_step_shows_success_indicator()
    {
        $output = new BufferedOutput;
        step('Test step', function () {
            // no-op
        }, $output);

        $result = $output->fetch();
        $this->assertStringContainsString('Test step', $result);
        $this->assertStringContainsString('✅', $result);
    }

    public function test_step_shows_failure_indicator_and_rethrows_when_critical()
    {
        $output = new BufferedOutput;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Something broke');

        step('Failing step', function () {
            throw new \RuntimeException('Something broke');
        }, $output, false, true);
    }

    public function test_step_shows_failure_but_continues_when_non_critical()
    {
        $output = new BufferedOutput;

        // Should NOT throw
        step('Non-critical step', function () {
            throw new \RuntimeException('Minor failure');
        }, $output, false, false);

        $result = $output->fetch();
        $this->assertStringContainsString('❌', $result);
        $this->assertStringContainsString('Minor failure', $result);
    }

    public function test_step_verbose_shows_trace_on_failure()
    {
        $output = new BufferedOutput;

        step('Verbose failing step', function () {
            throw new \RuntimeException('Trace test');
        }, $output, true, false);

        $result = $output->fetch();
        $this->assertStringContainsString('Trace:', $result);
    }

    public function test_run_command_succeeds()
    {
        // Should not throw
        runCommand('echo hello');
        $this->assertTrue(true);
    }

    public function test_run_command_throws_on_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Command failed');

        runCommand('exit 1');
    }

    public function test_run_command_verbose_uses_passthru()
    {
        // Should not throw - just verifying it works in verbose mode
        ob_start();
        runCommand('echo verbose-test', true);
        $output = ob_get_clean();
        $this->assertStringContainsString('verbose-test', $output);
    }

    public function test_ensure_dir_creates_directory()
    {
        $dir = sys_get_temp_dir().'/kickoff-test-'.uniqid();
        $this->assertDirectoryDoesNotExist($dir);

        ensureDir($dir);
        $this->assertDirectoryExists($dir);

        rmdir($dir);
    }

    public function test_ensure_dir_noop_if_exists()
    {
        $dir = sys_get_temp_dir();
        // Should not throw or change anything
        ensureDir($dir);
        $this->assertDirectoryExists($dir);
    }

    public function test_put_file_writes_content()
    {
        $file = sys_get_temp_dir().'/kickoff-test-'.uniqid().'.txt';
        putFile($file, 'hello world');

        $this->assertFileExists($file);
        $this->assertEquals('hello world', file_get_contents($file));

        unlink($file);
    }

    public function test_copy_recursively_copies_directory_tree()
    {
        $src = sys_get_temp_dir().'/kickoff-src-'.uniqid();
        $dst = sys_get_temp_dir().'/kickoff-dst-'.uniqid();

        // Create source tree
        mkdir($src.'/subdir', 0755, true);
        file_put_contents($src.'/file.txt', 'root file');
        file_put_contents($src.'/subdir/nested.txt', 'nested file');

        copyRecursively($src, $dst);

        $this->assertFileExists($dst.'/file.txt');
        $this->assertFileExists($dst.'/subdir/nested.txt');
        $this->assertEquals('root file', file_get_contents($dst.'/file.txt'));
        $this->assertEquals('nested file', file_get_contents($dst.'/subdir/nested.txt'));

        // Cleanup
        unlink($dst.'/subdir/nested.txt');
        unlink($dst.'/file.txt');
        rmdir($dst.'/subdir');
        rmdir($dst);
        unlink($src.'/subdir/nested.txt');
        unlink($src.'/file.txt');
        rmdir($src.'/subdir');
        rmdir($src);
    }
}
