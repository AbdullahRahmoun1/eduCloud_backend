<?php

use App\Models\Address;
use App\Models\Bus;
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
        Schema::create('bus_address', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bus::class);
            $table->foreignIdFor(Address::class);
            $table->unsignedInteger('price');
            $table->unique(['bus_id','address_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_addresses');
    }
};
