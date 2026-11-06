<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('site_address')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('architect')->nullable();
            $table->string('date')->nullable();
            $table->string('quotation_no')->nullable();
            $table->string('designer')->nullable();
            $table->string('sales_contact')->nullable();
            $table->string('model')->nullable();
            $table->string('f_base')->nullable();
            $table->string('f_wall')->nullable();
            $table->string('f_tall')->nullable();
            $table->string('carcase_combination')->nullable();
            $table->string('glass')->nullable();
            $table->string('fillers_panels')->nullable();
            $table->string('drawers_internals')->nullable();
            $table->string('handles')->nullable();
            $table->string('plinth_colour')->nullable();
            $table->string('cornish')->nullable();
            $table->string('plinth_height')->nullable();
            $table->string('kitchen_appliances')->nullable();
            $table->string('sitchen_sink')->nullable();
            $table->string('core_cutting')->nullable();
            $table->string('worktop')->nullable();
            $table->string('kitchen_furniture')->nullable();
            $table->string('customer_pan_no')->nullable();
            $table->string('billing_name_no')->nullable();
            $table->string('project')->nullable();
            $table->string('worktop_height')->nullable();
            $table->string('appliances_sink_accessories')->nullable();
            $table->string('worktop_dado')->nullable();
            $table->string('ducting_gas_piping')->nullable();
            $table->string('pdf_name')->nullable();
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
        Schema::dropIfExists('pdfs');
    }
}
