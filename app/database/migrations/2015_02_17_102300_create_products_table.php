<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
    	{
        	$table->increments('id');
        	$table->softDeletes();
	     	$table->integer('restorer_id')->unsigned();
	     	$table->integer('tva_id')->unsigned();
	     	$table->integer('category_id')->unsigned()->nullable();
	     	$table->string('name');
	     	$table->string('short_name');
	     	$table->text('description');
	     	$table->boolean('is_displayed');
	     	$table->integer('position');
	     	$table->float('price');
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
		Schema::drop('products');
	}

}
