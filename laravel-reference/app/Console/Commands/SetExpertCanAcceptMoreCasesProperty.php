<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\MoreCasesTrait;
use Illuminate\Console\Command;

class SetExpertCanAcceptMoreCasesProperty extends Command
{
    use MoreCasesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expert:set_can_accept_more_cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expert can accept more cases property';

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
        $experts = User::query()
            ->where('type', 'expert')
            ->where('locked', 0)
            ->has('expert_data')
            ->with('expert_data')
            ->get();

        foreach ($experts as $expert) {
            $this->setCanAcceptMoreCases($expert);
        }

        return self::SUCCESS;
    }
}
