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
            $table->string('mother_name',20);
            $table->string('place_of_living',45)->nullable();
            $table->date('birth_date')->default(now());
            $table->string('birth_place',45)->nullable();
            $table->float('6th_grade_avg')->nullable()->default(10);
            //TODO: social should be string or enum???
            $table->enum('social_description',['متزوجين','مطلقين','أرمل'])->default('متزوجين');
            $table->string('grand_father_name',30)->nullable();
            $table->string('mother_last_name',30)->nullable();
            $table->integer('public_record')->nullable();
            $table->boolean('father_alive')->nullable();
            $table->boolean('mother_alive')->nullable();
            $table->string('father_profession',30)->nullable();
            $table->string('previous_school',30)->nullable();
            //TODO: address id should be foreign key
            $table->integer('address_id')->nullable();
            $table->boolean('transportation_subscriber')->nullable();
            $table->string('registration_place',40)->nullable();
            $table->integer('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('notes',200)->nullable();
            $table->unique(['first_name', 'last_name', 'father_name', 'mother_name']);
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
