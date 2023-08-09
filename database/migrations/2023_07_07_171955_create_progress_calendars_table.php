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
        Schema::create('progress_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_calendar_id')->constrained();
            $table->foreignId('g_class_id')->constrained();
            $table->unique(['g_class_id', 'base_calendar_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_calendars');
    }
};
