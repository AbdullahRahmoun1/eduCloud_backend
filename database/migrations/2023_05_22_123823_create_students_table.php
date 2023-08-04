<?php

use App\Models\Grade;
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
            $table->foreignId('g_class_id')->nullable()->constrained();
            $table->foreignIdFor(Grade::class)->constrained();
            $table->string('first_name',20);
            $table->string('last_name',20);
            $table->string('father_name',20);
            $table->string('mother_name',20);
            $table->string('place_of_living',45)->nullable();
            $table->date('birth_date')->default(now());
            $table->string('birth_place',45)->nullable();
            $table->float('6th_grade_avg')->nullable()->default(10);
            $table->string('social_description', 65)->nullable();
            $table->string('grand_father_name',30)->nullable();
            $table->string('mother_last_name',30)->nullable();
            $table->string('public_record', 30)->nullable();
            $table->boolean('father_alive')->nullable();
            $table->boolean('mother_alive')->nullable();
            $table->string('father_profession',30)->nullable();
            $table->string('previous_school',30)->nullable();
            $table->foreignId('address_id')->nullable()->constrained();
            $table->boolean('transportation_subscriber')->nullable();
            $table->string('registration_place',40)->nullable();
            $table->string('registration_number',30)->nullable();
            $table->date('registration_date')->nullable();
            $table->string('notes',200)->nullable();
            $table->unique(['first_name', 'last_name', 'father_name', 'mother_name','grade_id'],"Duplicate student");
            $table->timestamps();
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
