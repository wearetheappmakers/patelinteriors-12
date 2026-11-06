<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemainderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remainder', function (Blueprint $table) {
            $table->increments('id');
               $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
             $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
             $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('client')->onDelete('cascade');
             $table->integer('quotation_id')->unsigned()->nullable();
            $table->foreign('quotation_id')->references('id')->on('quotation')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->string('quotation_date')->nullable();
           
            $table->string('status')->nullable();
           
            $table->string('final_amount')->nullable();
            
            $table->integer('lead_id')->nullable();
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
        Schema::dropIfExists('remainder');
    }
}
