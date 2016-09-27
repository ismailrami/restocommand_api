<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestorersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('restorers', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
        	$table->integer('user_id')->unsigned();
	     	$table->string('name_restaurant')->unique();
    	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('restorers');
	}

}
