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
        Schema::table('wos_answers', function (Blueprint $table): void {
            $table->unsignedBigInteger('country_id')->nullable()->after('case_id');
            $table->unsignedBigInteger('company_id')->nullable()->after('country_id');
            $table->timestamp('updated_at')->after('answer_6')->useCurrent();

            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wos_answers', function (Blueprint $table): void {
            //
        });
    }
};
