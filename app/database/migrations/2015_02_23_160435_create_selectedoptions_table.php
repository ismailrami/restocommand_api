<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectedoptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('selectedoptions', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->integer('orderline_id')->unsigned();
        	$table->integer('option_id')->unsigned();
        	$table->text('values');
    	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selectedoptions');
	}

}
