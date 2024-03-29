<?php

use App\Models\AbilityTest;
use App\Models\Student;
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
        Schema::create('at_marks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('student_id');
            $table->string('student_type',45)->default(Student::class);
            $table->boolean('is_entry_mark')->default(false);
            $table->foreignIdFor(AbilityTest::class);
            $table->unique(['date','student_id','student_type','ability_test_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('at_marks');
    }
};
