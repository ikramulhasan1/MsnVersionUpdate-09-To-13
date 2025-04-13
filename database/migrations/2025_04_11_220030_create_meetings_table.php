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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->time('meeting_time');
            $table->date('date');
            $table->string('city')->nullable();
            $table->string('ip')->nullable();
            $table->string('distance_km')->nullable();
            $table->string('distance_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
