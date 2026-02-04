<?php

namespace App\Console\Commands;

use App\Models\Inactivity;
use Illuminate\Console\Command;

class ReactivateInactiveExperts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expert:reactive-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivate inactive experts.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Inactivity::query()
            ->with('user')
            ->where('until', '<=', now())
            ->get()->each(function (Inactivity $inactivity): void {
                $inactivity->user->update(['active' => 1]);
                $inactivity->delete();
            });
    }
}
