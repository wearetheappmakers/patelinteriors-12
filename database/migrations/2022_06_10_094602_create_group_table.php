<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('pc')->nullable();
            $table->string('user')->nullable();
            $table->string('product')->nullable();
            $table->string('lead')->nullable();
            $table->string('complain')->nullable();
            $table->string('quot')->nullable();
            $table->string('super')->nullable();
            $table->string('employee')->nullable();
            $table->string('distri')->nullable();
            $table->string('section')->nullable();
            $table->string('stage')->nullable();
            $table->string('source')->nullable();
            $table->string('gst')->nullable();
            $table->string('client')->nullable();
            $table->string('quotation_footer')->nullable();
            $table->string('contact')->nullable();
            $table->string('amc')->nullable();
            $table->string('task')->nullable();
            $table->string('member')->nullable();
            $table->string('template')->nullable();
            $table->string('a_keyword')->nullable();
            $table->string('system_a')->nullable();
            $table->string('autoresponder')->nullable();
            $table->timestamps();
        });

         DB::table('group')->insert([
           
            'name' => "admin",
            'pc' => "1,1,1,1",
            'user' => "1,1,1,1",
            'product' => "1,1,1,1",
            'lead' => "1,1,1,1",
            'complain' => "1,1,1,1",
            'quot' => "1,1,1,1",
            'super' => "1,1,1,1",
            'employee' => "1,1,1,1",
            'distri' => "1,1,1,1",
            'section' => "1,1,1,1",
            'stage' => "1,1,1,1",
            'stage' => "1,1,1,1",
            'source' => "1,1,1,1",
            'gst' => "1,1,1,1",
            'client' => "1,1,1,1",
            'quotation_footer' => "1,1,1,1",
            'contact' => "1,1,1,1",
            'amc' => "1,1,1,1",
            'task' => "1,1,1,1",
            'member' => "1,1,1,1",
            'template' => "1,1,1,1",
            'a_keyword' => "1,1,1,1",
            'system_a' => "1,1,1,1",
            'autoresponder' => "1,1,1,1",
            

           

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group');
    }
}
