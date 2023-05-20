<?php

use App\Models\AtMark;
use App\Models\AtSection;
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
        Schema::create('at_mark_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('mark');
            $table->foreignIdFor(AtMark::class);
            //FIXME :atsection relation can be removed if its unessecery
            $table->foreignIdFor(AtSection::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('at_mark_sections');
    }
};
