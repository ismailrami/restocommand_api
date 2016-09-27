<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tvas', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
	     	$table->integer('restorer_id')->unsigned();
	     	$table->string('name');
	     	$table->float('value');
    	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tvas');
	}

}
