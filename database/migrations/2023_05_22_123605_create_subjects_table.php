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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name',45);
            $table->string('notes',100)->nullable();
            $table->integer('min_mark')->default(60);
            $table->integer('max_mark')->default(100);
            $table->foreignId('grade_id')->constrained();
            $table->unique(['name','grade_id'],'name_grade_unique');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
