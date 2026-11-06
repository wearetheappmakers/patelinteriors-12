<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadBasicChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_basic_child', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
             $table->integer('productcategory_id')->unsigned()->nullable();
            $table->foreign('productcategory_id')->references('id')->on('product_category')->onDelete('cascade');
             $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
             $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
             $table->integer('warehouse_id')->nullable();
            $table->string('qty')->nullable();  
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
        Schema::dropIfExists('lead_basic_child');
    }
}
