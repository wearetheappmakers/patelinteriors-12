<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_log', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('quotation_id')->unsigned()->nullable();
            $table->foreign('quotation_id')->references('id')->on('quotation')->onDelete('cascade');
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->string('qty')->default(0);
            $table->string('used')->default(0);
            $table->string('date')->default(0);
            $table->string('return')->default(0);

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
        Schema::dropIfExists('delivery_log');
    }
}
