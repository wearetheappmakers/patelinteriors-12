<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadBasicDesignChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_basic_design_child', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('design')->nullable();            
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
        Schema::dropIfExists('lead_basic_design_children');
    }
}
