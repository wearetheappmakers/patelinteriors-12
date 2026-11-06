<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_document', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('client_id')->unsigned()->nullable();

            $table->foreign('client_id')->references('id')->on('client')->onDelete('cascade');
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('lead')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('pan_card')->nullable();
            $table->string('cancelled_cheque')->nullable();
            $table->string('rtgs_details')->nullable();
            $table->string('form_submission')->nullable();
            $table->string('status')->default(0);
            $table->string('tracking_no')->nullable();
            $table->string('tracking_link')->nullable();
            $table->string('distributor_id')->nullable();
            $table->string('remark')->nullable();
            $table->string('machine_delivered')->nullable();

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
        Schema::dropIfExists('client_document');
    }
}
