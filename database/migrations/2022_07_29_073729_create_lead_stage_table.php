<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_stage', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
             $table->integer('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('status')->default(0);
            $table->string('is_completed')->nullable();
            $table->string('is_canceled')->nullable();
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
        Schema::dropIfExists('lead_stage');
    }
}
