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
        Schema::create('numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number',20);
            $table->string('owner_type',60);
            $table->integer('owner_id');
            $table->enum('type',[
                'father', 'mother', 'home',
                'sms', 'telegram',
                'extra', 'other'
            ]);
            $table->string('relationship',50)->nullable();
            $table->unique(['number','owner_type','owner_id']);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbers');
    }
};
