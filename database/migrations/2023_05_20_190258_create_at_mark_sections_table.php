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
            $table->foreignIdFor(AtSection::class);
            $table->unique(['at_mark_id','at_section_id']);
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
