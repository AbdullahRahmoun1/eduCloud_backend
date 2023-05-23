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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('g_class_id')->constrained();
            $table->string('first_name',20);
            $table->string('last_name',20);
            $table->string('father_name',20);
            $table->string('mother_name',30);
            $table->string('place_of_living',50)->default('smwere');
            $table->date('birth_date')->default(now());
            $table->float('6th_grade_avg')->default(10);
            $table->enum('social_description',['متزوجين','مطلقين','أرمل'])->default('متزوجين');
            $table->timestamps();

            $table->unique(['first_name', 'last_name', 'father_name', 'mother_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
