<?php

namespace App\Console\Commands;

use App\Mail\AccountAdminAlert\DirectInvoiceDataUpdate;
use App\Mail\AccountAdminAlert\WorkshopCrisisOtherActivityClose;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAccountAdminAlertEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-admins:sent-alert-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send account admin alert emails';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::query()->where('type', 'account_admin')->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new WorkshopCrisisOtherActivityClose($user));
            Mail::to($user->email)->send(new DirectInvoiceDataUpdate($user));
        }

        return Command::SUCCESS;
    }
}
