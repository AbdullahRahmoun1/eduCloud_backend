<?php

use App\Models\AbilityTest;
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
            $table->integer('full_mark');
            $table->date('date');
            $table->integer('student_id');
            $table->string('student_type',45);
            $table->foreignIdFor(AbilityTest::class);
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
