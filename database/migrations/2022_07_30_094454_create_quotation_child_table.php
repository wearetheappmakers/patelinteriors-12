<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_child', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('quotation_id')->unsigned()->nullable();
            $table->foreign('quotation_id')->references('id')->on('quotation')->onDelete('cascade');
             $table->integer('productcategory_id')->unsigned()->nullable();
            $table->foreign('productcategory_id')->references('id')->on('product_category')->onDelete('cascade');
            $table->integer('company_id')->nullable();
             $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
          $table->string('warehouse_id')->nullable();
            $table->string('price')->nullable();
            $table->string('qty')->nullable();
            $table->string('value_of_amount')->nullable();
            $table->string('discount')->nullable();
            $table->integer('gst_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('total')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('quotation_child');
    }
}
