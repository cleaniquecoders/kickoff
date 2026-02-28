<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\DefaultMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestSendMail extends Command
{
    protected $signature = 'app:test-send-mail {email} {--queue=sync : Type of queue. Default is sync.}';

    protected $description = 'Send test email to given ';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mail = (new DefaultMail('Test E-mail', 'Hello World'));
        $email = $this->argument('email');
        $name = str($this->argument('email'))->before('@')->title()->toString();

        if ($this->option('queue')) {
            $mail->onQueue($this->option('queue'));
        }

        Mail::to($email, $name)->send($mail);

        return self::SUCCESS;
    }
}
