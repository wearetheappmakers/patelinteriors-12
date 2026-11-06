<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadFollowupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_followup', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
             $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->string('description')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('type')->nullable();
            $table->string('is_completed')->nullable();
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
        Schema::dropIfExists('lead_followup');
    }
}
