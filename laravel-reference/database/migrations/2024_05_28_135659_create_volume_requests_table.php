<?php

use App\Enums\VolumeRequestStatusEnum;
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
        Schema::create('volume_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('volume_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('headcount')->nullable();
            $table->date('date');
            $table->string('status')->default(VolumeRequestStatusEnum::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volume_requests');
    }
};
