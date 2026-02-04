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
        Schema::create('direct_billing_datas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();

            $table->tinyInteger('billing_frequency');
            $table->string('currency');
            $table->string('vat_rate')->nullable();

            $table->boolean('send_invoice_by_post')->default(false);
            $table->boolean('send_completion_certificate_by_post')->default(false);

            $table->string('post_code');
            $table->string('country');
            $table->string('city');
            $table->string('street');
            $table->string('house_number');

            $table->boolean('send_invoice_by_email')->default(false);
            $table->boolean('send_completion_certificate_by_email')->default(false);

            $table->boolean('upload_invoice_online')->default(false);
            $table->string('invoice_online_url');

            $table->boolean('upload_completion_certificate_online')->default(false);
            $table->string('completion_certificate_online_url');

            $table->string('contact_holder_name');
            $table->boolean('show_contact_holder_name_on_post')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_billing_data');
    }
};
