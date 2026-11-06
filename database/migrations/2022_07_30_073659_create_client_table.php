<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
             $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('member')->nullable();
            $table->string('member_code')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('other_mobile_no')->nullable();
            $table->string('email')->nullable();
        
            $table->integer('referred_type')->unsigned()->nullable();
            $table->foreign('referred_type')->references('id')->on('lead_source')->onDelete('cascade');
            $table->string('dob')->nullable();
            $table->string('is_archive')->default(0);
            $table->string('status')->nullable();
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
        Schema::dropIfExists('client');
    }
}
