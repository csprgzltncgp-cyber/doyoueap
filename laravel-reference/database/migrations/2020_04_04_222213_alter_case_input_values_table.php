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
        Schema::table('case_input_values', function (Blueprint $table): void {
            //

            $table->unsignedBigInteger('riport_category_id')->after('case_input_id')->nullable();
            $table->unsignedBigInteger('riport_subcategory_id')->after('riport_category_id')->nullable();
            $table->foreign('riport_category_id')->references('id')->on('riport_categories');
            $table->foreign('riport_subcategory_id')->references('id')->on('riport_subcategories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_input_values', function (Blueprint $table): void {
            //
        });
    }
};
