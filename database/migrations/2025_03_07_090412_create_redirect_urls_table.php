<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redirect_urls', function (Blueprint $table) {
            $table->id();
            $table->string('submitted_url', 255)->unique(); // URL submitted by the user
            $table->string('redirect_to'); // The URL where it should redirect
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redirect_urls');
    }
};
