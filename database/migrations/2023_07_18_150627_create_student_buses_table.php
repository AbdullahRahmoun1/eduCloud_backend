<?php

use App\Models\Bus;
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
        Schema::create('student_bus', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->unique();
            $table->foreignIdFor(Bus::class);
            $table->unique(['student_id','bus_id'],'Student can have one bus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_buses');
    }
};
