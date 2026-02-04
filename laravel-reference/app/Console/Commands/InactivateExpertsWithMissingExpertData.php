<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InactivateExpertsWithMissingExpertData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'experts:inactivate-with-missing-expert-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactivate experts with missing expert data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (date('m') == 10) {
            $experts = User::query()->where('type', 'expert')->get();

            foreach ($experts as $expert) {
                if ($expert->has_missing_expert_data()) {
                    $expert->active = false;
                    $expert->save();
                }
            }
        }

        return self::SUCCESS;
    }
}
