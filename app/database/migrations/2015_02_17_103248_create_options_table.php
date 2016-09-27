<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('options', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
	     	$table->string('name');
	     	$table->integer('restorer_id')->unsigned();
	     	$table->integer('number_min')->nullable();
	     	$table->integer('number_max')->nullable();
	     	$table->boolean('is_multiple');
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
		Schema::drop('options');
	}

}
