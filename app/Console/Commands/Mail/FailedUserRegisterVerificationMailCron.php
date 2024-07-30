<?php

namespace App\Console\Commands\Mail;

use App\Mail\ErrorCronJobMail;
use App\Mail\User\UserRegisterVerificationMail;
use App\Models\Admin;
use App\Models\FailedUserEmailVerificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FailedUserRegisterVerificationMailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'failedMail:userRegisterVerification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To resend failed sending email from user register verification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $failedEmails = FailedUserEmailVerificationMail::get();

            foreach ($failedEmails as $key => $failedEmail) {
                $data                         = [];
                $data['first_name']           = $failedEmail->first_name;
                $data['email_validation_url'] = $failedEmail->email_validation_url;
        
                Mail::to($failedEmail['email'])->send((new UserRegisterVerificationMail($data)));
            }

            Log::info('Success Cron Job - Failed Email - User Email Verification');
        } catch (\Exception $e) {
            Log::info('Error Cron Job - Failed Email - User Email Verification');

            Log::info($e->getMessage());

            $dataError                   = [];
            $dataError['failed_cronjob'] = 'Failed Email - User Email Verification';

            $admin = Admin::where('id', request()->user()->id)->first();
            
            Mail::to($admin->email)->send((new ErrorCronJobMail($dataError)));
        }
    }
}
