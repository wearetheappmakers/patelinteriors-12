<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_assign', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
            $table->integer('stage_id')->unsigned()->nullable();
            $table->foreign('stage_id')->references('id')->on('lead_stage')->onDelete('cascade');

            $table->integer('section_id')->unsigned()->nullable();
            $table->foreign('section_id')->references('id')->on('section')->onDelete('cascade');
            
            // $table->integer('employee_id')->unsigned()->nullable();
            // $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('employee_id')->nullable();
            $table->string('note')->nullable();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('lead_assign');
    }
}
