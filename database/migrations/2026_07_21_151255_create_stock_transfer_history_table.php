<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTransferHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_history', function (Blueprint $table) {
            $table->bigIncrements('id');
              $table->date('transfer_date');
            // Branch
            $table->unsignedBigInteger('branch_id');
            // Warehouse
            $table->unsignedBigInteger('from_warehouse_id');

            $table->unsignedBigInteger('to_warehouse_id');
            // Product
            $table->unsignedBigInteger('product_id');
            // Quantity movement
            $table->integer('qty')->default(0);
            // Separate movement columns
            $table->integer('inward')->default(0);
            $table->integer('outward')->default(0);
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
        Schema::dropIfExists('stock_transfer_history');
    }
}
