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
        Schema::create('at_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name',45);
            $table->tinyInteger('max_mark');
            $table->tinyInteger('min_mark');
            $table->foreignIdFor(AbilityTest::class)->nullable();
            $table->timestamps();
            $table->unique(['ability_test_id','name']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('at_sections');
    }
};
