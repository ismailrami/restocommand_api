<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->integer('table_id')->unsigned()->nullable();
        	$table->integer('worker_id')->unsigned()->nullable();
        	$table->integer('number_persone');
        	$table->integer('is_take_away');
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
		Schema::drop('orders');
	}

}
