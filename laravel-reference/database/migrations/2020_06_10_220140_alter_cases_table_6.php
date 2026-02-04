<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table): void {
            //
            $table->tinyInteger('email_sent_3months')->default(0)->comment('Kiküldtük-e a 3 hónapos emailt')->after('closed_by_expert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table): void {
            //
        });
    }
};
