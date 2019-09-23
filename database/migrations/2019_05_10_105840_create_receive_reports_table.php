<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiveReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('orders_id');
            $table->bigInteger('stock_reports_id');
            $table->bigInteger('users_id');
            $table->decimal('quantity', 10, 0);
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
        Schema::dropIfExists('receive_reports');
    }
}
