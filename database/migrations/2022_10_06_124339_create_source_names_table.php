<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourceNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('source_names', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_sourceid')->nullable()->unsigned();
            $table->foreign('lead_sourceid')->references('id')->on('lead_source')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('source_names');
    }
}
