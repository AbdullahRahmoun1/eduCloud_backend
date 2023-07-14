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
        Schema::create('candidate_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained();
            $table->string('first_name',20);
            $table->string('last_name',20);
            $table->string('father_name',20);
            $table->string('mother_name',30);
            $table->string('place_of_living',50)->nullable();
            $table->date('birth_date')->default(now());
            $table->float('6th_grade_avg')->default(10);
            $table->boolean('rejected')->nullable();
            $table->string('reason',100)->nullable();
            $table->unique(['first_name', 'last_name', 'father_name', 'mother_name'], 'unique student');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_students');
    }
};
