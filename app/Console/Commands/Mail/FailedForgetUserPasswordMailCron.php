<?php

namespace App\Console\Commands\Mail;

use Illuminate\Console\Command;

class FailedForgetUserPasswordMailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedMail:forgetUserPassword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To resend failed sending email from forget user password';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
