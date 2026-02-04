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
        Schema::table('companies', function (Blueprint $table): void {
            //
            $table->unsignedBigInteger('contact_holder_id')->after('name')->nullable();
            $table->text('orgId')->after('contact_holder_id')->nullable();

            $table->foreign('contact_holder_id')->references('id')->on('contact_holders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            //
        });
    }
};
