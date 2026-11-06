<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadCommunicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_communication', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
             $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('email')->nullable();
            $table->string('autoresponder')->nullable();
            $table->string('subject')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('lead_communication');
    }
}
