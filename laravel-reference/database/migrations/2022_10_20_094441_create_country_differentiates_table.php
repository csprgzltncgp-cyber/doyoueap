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
        Schema::create('country_differentiates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->boolean('contract_holder')->default(false);
            $table->boolean('org_id')->default(false);
            $table->boolean('contract_date')->default(false);
            $table->boolean('reporting')->default(false);
            $table->boolean('invoicing')->default(false);
            $table->timestamps();

            if (config('app.env') != 'local') {
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_differentiates');
    }
};
