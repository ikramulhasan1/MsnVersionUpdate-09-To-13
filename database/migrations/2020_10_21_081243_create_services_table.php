<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->string('short_title', 30)->unique();
            $table->string('price')->nullable();
            $table->string('starting_price')->nullable();
            $table->string('priceCurrency')->default('USD');
            $table->string('average_rating')->default(4.5);
            $table->string('review_count')->default(150);
            $table->string('slug')->unique();
            $table->text('short_desc')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->boolean('manu')->default('0');
            $table->boolean('status')->default('1');
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
        Schema::dropIfExists('services');
    }
}
