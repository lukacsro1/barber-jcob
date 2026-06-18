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
        Schema::create('barber_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->boolean('is_working')->default(true);
            $table->time('start_time')->nullable()->default('09:00:00');
            $table->time('end_time')->nullable()->default('17:00:00');
            $table->timestamps();

            // A barber should have only one schedule config per day of week
            $table->unique(['user_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barber_schedules');
    }
};
