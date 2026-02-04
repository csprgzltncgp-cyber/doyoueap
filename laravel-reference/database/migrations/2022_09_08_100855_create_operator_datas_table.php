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
        Schema::create('operator_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('position');
            $table->string('private_email');
            $table->string('company_email')->nullable();
            $table->string('private_phone');
            $table->string('company_phone');
            $table->tinyInteger('employment_type');
            $table->string('bank_account_number')->nullable();
            $table->string('eap_chat_username');
            $table->string('eap_chat_password');

            if (config('app.env') != 'local') {
                $table->foreign('user_id')->references('id')->on('users');
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opeartor_data');
    }
};
