<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('get_quotes', function (Blueprint $table) {
            $table->bigIncrements('id')->start_from(20000);
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->integer('prefer_contact')->nullable();
            $table->string('quantity')->nullable();
            $table->text('message')->nullable();
            $table->text('file_path')->nullable();
            $table->string('pre_delivery_time')->nullable();
            $table->string('where_find')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->date('invoice_time')->nullable();
            $table->integer('mail_status')->default('0');
            $table->integer('status')->default('1');
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
        Schema::dropIfExists('get_quotes');
    }
}
