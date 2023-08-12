<?php

use App\Models\Category;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id');
            $table->string('owner_type',60);
            $table->string('body',300);
            $table->boolean('sent_successfully')->default(true); 
            $table->boolean('approved')->default(true);   
            $table->dateTime('date');
            $table->foreignIdFor(Category::class);  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
