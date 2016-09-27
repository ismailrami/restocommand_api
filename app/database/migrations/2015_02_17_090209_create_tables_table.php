<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tables', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
	     	$table->integer('area_id')->unsigned();
	     	$table->string('name');
	     	$table->integer('width');
	     	$table->integer('height');
	     	$table->integer('coordinate_x');
	     	$table->integer('coordinate_y');
	     	$table->boolean('is_open');
	     	$table->string('shape');
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
		Schema::drop('tables');
	}

}
