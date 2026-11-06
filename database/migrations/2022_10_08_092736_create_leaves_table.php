<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->nullable()->unsigned();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('employee_id')->nullable()->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('from_date')->nullable();
            $table->string('to_date')->nullable();
            $table->string('approved_from_date')->nullable();
            $table->string('approved_to_date')->nullable();
            $table->string('subject')->nullable();
            $table->string('reason')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('leaves');
    }
}
