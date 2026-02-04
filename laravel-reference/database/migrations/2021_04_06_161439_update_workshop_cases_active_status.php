<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $workshops = DB::table('workshops')->get();
        $inactive_workshops = DB::table('workshop_cases')
            ->leftJoin('workshops', 'workshop_cases.select_activity_id', 'workshops.activity_id')
            ->select(['workshops.id as id'])->get();

        foreach ($workshops as $workshop) {
            DB::table('workshops')->where(['id' => $workshop->id])->update(['active' => 1]);
        }

        foreach ($inactive_workshops as $workshop) {
            DB::table('workshops')->where(['id' => $workshop->id])->update(['active' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
