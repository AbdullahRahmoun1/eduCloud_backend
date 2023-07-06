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
        Schema::create('g_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained();
            $table->string('name',30);
            $table->integer('max_number')->unsigned();
            $table->unique(['grade_id', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('g_classes');
    }
};
