<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id')->start_from(20000);
            $table->bigInteger('quote_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('company')->nullable();
            $table->decimal('total_amount', 8, 2)->default('0');
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->decimal('invoice_amount', 8, 2)->default('0');
            $table->decimal('service_charge', 8, 2)->nullable();
            $table->decimal('tax', 8, 2)->nullable();
            $table->decimal('shipping', 8, 2)->nullable();
            $table->dateTime('invoice_date');
            $table->dateTime('due_date')->nullable();
            $table->text('message')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('reference')->nullable();
            $table->text('attach')->nullable();
            $table->integer('invoice_type')->nullable();
            $table->boolean('estimate_flag')->default('0');
            $table->integer('status')->default('1');
            $table->foreign('quote_id')
                  ->references('id')->on('get_quotes')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('invoices');
    }
}
