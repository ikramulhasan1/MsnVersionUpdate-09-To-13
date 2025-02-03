<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_category', function (Blueprint $table) {
                $table->integer('portfolio_id')->unsigned();
                $table->integer('portfolio_category_id')->unsigned();
                $table->foreign('portfolio_id')
                      ->references('id')->on('portfolios')
                      ->onDelete('cascade');
                $table->foreign('portfolio_category_id')
                      ->references('id')->on('portfolio_categories')
                      ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolio_category');
    }
}
