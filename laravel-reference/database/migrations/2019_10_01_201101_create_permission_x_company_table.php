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
        Schema::create('permission_x_company', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('permission_id')->comment('Megadja, hogy melyik jogosultság');
            $table->unsignedBigInteger('company_id')->comment('Megadja, hogy melyik cég');
            $table->integer('number')->comment('Megadja, hogy adott cégnek hány egység van adott jogosultságból');
            $table->integer('duration')->comment('Megadja, hogy hány perc egy alkalom');
            $table->enum('contact', ['skype', 'phone', 'personal', 'skype-phone-personal', 'skype-phone', 'skype-personal', 'phone-personal', 'phone-email'])->comment('Megadja, hogy a kapcsolatfeltével hogy történhet');
            $table->timestamps();

            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_x_company');
    }
};
