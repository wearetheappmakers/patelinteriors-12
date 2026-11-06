<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('group')->onDelete('cascade');
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('image')->nullable();
            $table->string('email')->unique();
            $table->string('number')->nullable();
             $table->integer('role');
            $table->integer('status')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('show_password')->nullable();
           
            $table->rememberToken();
            $table->timestamps();
        });

         DB::table('users')->insert([
            'group_id' => 1,
            'name' => "admin",
            'number' => 1234567890,
            'email' => 'admin@gmail.com',
            'role' => 1,
            'status' => 1,
            'password' => bcrypt('admin@123'),
            'show_password' => 'admin@123',
           

        ]);
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
