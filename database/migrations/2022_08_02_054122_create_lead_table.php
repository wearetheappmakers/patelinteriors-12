<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('client')->onDelete('cascade');
            $table->string('lead_name')->nullable();
            $table->string('description')->nullable();
            $table->integer('source_id')->unsigned()->nullable();
            $table->foreign('source_id')->references('id')->on('lead_source')->onDelete('cascade');
            $table->string('source_name')->nullable();
            $table->integer('productcategory_id')->unsigned()->nullable();
            $table->foreign('productcategory_id')->references('id')->on('product_category')->onDelete('cascade');
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->string('no_of_machines')->nullable();
            $table->string('date')->nullable();
            $table->string('current_project_phase')->nullable();
            $table->string('lead_value')->nullable();
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
        Schema::dropIfExists('lead');
    }
}
