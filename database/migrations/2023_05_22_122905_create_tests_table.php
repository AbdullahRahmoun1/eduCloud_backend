<?php

use App\Models\GClass;
use App\Models\ProgressCalendar;
use App\Models\Subject;
use App\Models\Type;
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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->integer('min_mark');
            $table->integer('max_mark');
            $table->date('date');
            $table->foreignIdFor(Subject::class);
            $table->foreignIdFor(GClass::class);
            $table->foreignIdFor(Type::class);
            $table->foreignIdFor(ProgressCalendar::class)->unique()->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
