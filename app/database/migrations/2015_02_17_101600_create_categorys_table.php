<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategorysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categorys', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
	     	$table->integer('restorer_id')->unsigned();
	     	$table->integer('category_id')->unsigned()->nullable();
	     	$table->string('name');
	     	$table->boolean('is_displayed');
	     	$table->integer('position');
	     	$table->string('color');
    	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categorys');
	}

}
