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
        Schema::table('operator_datas', function (Blueprint $table): void {
            // invoices
            $table->string('invoincing_name')->after('bank_account_number')->nullable();
            $table->string('invoincing_post_code')->after('invoincing_name')->nullable();
            $table->string('invoincing_country')->after('invoincing_post_code')->nullable();
            $table->string('invoincing_city')->after('invoincing_country')->nullable();
            $table->string('invoincing_street')->after('invoincing_city')->nullable();
            $table->string('invoincing_house_number')->after('invoincing_street')->nullable();
            $table->string('tax_number')->after('invoincing_house_number')->nullable();
            $table->string('language')->after('tax_number')->nullable();

            // employemnt
            $table->date('start_of_employment')->after('employment_type')->nullable();
            $table->integer('salary')->after('start_of_employment')->nullable();
            $table->string('salary_currency')->after('salary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operateor_datas', function (Blueprint $table): void {
            //
        });
    }
};
