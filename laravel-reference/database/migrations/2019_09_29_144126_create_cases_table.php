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
        Schema::create('cases', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->enum('status', ['opened', 'assigned_to_expert', 'employee_contacted', 'confirmed'])->default('opened');
            $table->unsignedBigInteger('company_id')->comment('Megadja, hogy melyik céghez tartozik az eset');
            $table->unsignedBigInteger('country_id')->comment('Megadja, hogy melyik országhoz tartozik az eset');
            $table->unsignedBigInteger('confirmed_by')->comment('Megadja, hogy ki hagyta jóvá')->nullable();
            $table->timestamp('confirmed_at')->nullable()->comment('Megadja, hogy mikor hagyták jóvá');
            $table->unsignedBigInteger('created_by')->comment('megadja, hogy ki hozta létre');
            $table->unsignedInteger('customer_satisfaction')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('confirmed_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
