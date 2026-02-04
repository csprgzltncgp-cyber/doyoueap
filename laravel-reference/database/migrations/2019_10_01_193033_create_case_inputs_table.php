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
        Schema::create('case_inputs', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name')->comment('Az input neve egy kiválasztott nyelven  [ez csak az adminban jelenik meg]');
            $table->unsignedBigInteger('company_id')->comment('Megadja, hogy melyik céghez tartozik az adott input; ha null, akkor mindegyikhez')->nullable();
            $table->enum('default_type', ['company_chooser', 'case_creation_time', 'case_type', 'client_name', 'location'])->nullable()->default(null);
            $table->enum('type', ['integer', 'date', 'double', 'text', 'select', 'multiple-list', 'boolean']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_inputs');
    }
};
