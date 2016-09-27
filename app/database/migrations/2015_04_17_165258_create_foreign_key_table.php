<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('role_user', function(Blueprint $table) {
			$table->foreign('role_id')->references('id')->on('roles')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('restorers', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('workers', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');			
		});
		Schema::table('tvas', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('areas', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('tables', function(Blueprint $table) {
			$table->foreign('area_id')->references('id')->on('areas')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('categorys', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('category_id')->references('id')->on('categorys')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('tva_id')->references('id')->on('tvas')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('category_id')->references('id')->on('categorys')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('pictures', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('options', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('option_product', function(Blueprint $table) {
			$table->foreign('option_id')->references('id')->on('options')
						->onDelete('restrict')
						->onUpdate('restrict');	
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('menus', function(Blueprint $table) {
			$table->foreign('restorer_id')->references('id')->on('restorers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('steps', function(Blueprint $table) {
			$table->foreign('menu_id')->references('id')->on('menus')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('product_step', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('step_id')->references('id')->on('steps')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->foreign('table_id')->references('id')->on('tables')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('worker_id')->references('id')->on('workers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('orderlines', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('order_id')->references('id')->on('orders')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('selectedoptions', function(Blueprint $table) {
			$table->foreign('orderline_id')->references('id')->on('orderlines')
						->onDelete('restrict')
						->onUpdate('restrict');
			$table->foreign('option_id')->references('id')->on('options')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('cachings', function(Blueprint $table) {
			$table->foreign('order_id')->references('id')->on('orders')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('role_user', function(Blueprint $table) {
			$table->dropForeign('role_user_role_id_foreign');
			$table->dropForeign('role_user_user_id_foreign');
		});
		Schema::table('areas', function(Blueprint $table) {
			$table->dropForeign('areas_restorer_id_foreign');
		});
		Schema::table('categorys', function(Blueprint $table) {
			$table->dropForeign('categorys_restorer_id_foreign');
			$table->dropForeign('categorys_category_id_foreign');
		});
		Schema::table('menus', function(Blueprint $table) {
			$table->dropForeign('menus_restorer_id_foreign');
		});
		Schema::table('options', function(Blueprint $table) {
			$table->dropForeign('options_restorer_id_foreign');
		});
		Schema::table('option_product', function(Blueprint $table) {
			$table->dropForeign('option_product_option_id_foreign');
			$table->dropForeign('option_product_product_id_foreign');
		});
		Schema::table('orderlines', function(Blueprint $table) {
			$table->dropForeign('orderlines_order_id_foreign');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->dropForeign('orders_table_id_foreign');
			$table->dropForeign('orders_worker_id_foreign');
		});
		Schema::table('pictures', function(Blueprint $table) {
			$table->dropForeign('pictures_product_id_foreign');
		});
		Schema::table('selectedoptions', function(Blueprint $table) {
			$table->dropForeign('product_orderlines_orderline_id_foreign');
			$table->dropForeign('product_orderlines_option_id_foreign');
		});
		Schema::table('product_step', function(Blueprint $table) {
			$table->dropForeign('product_step_product_id_foreign');
			$table->dropForeign('product_step_step_id_foreign');
		});
		Schema::table('restorers', function(Blueprint $table) {
			$table->dropForeign('restorers_user_id_foreign');
		});
		Schema::table('steps', function(Blueprint $table) {
			$table->dropForeign('steps_menu_id_foreign');
		});
		Schema::table('tables', function(Blueprint $table) {
			$table->dropForeign('tables_area_id_foreign');
		});
		Schema::table('tvas', function(Blueprint $table) {
			$table->dropForeign('tvas_restorer_id_foreign');
		});
		Schema::table('workers', function(Blueprint $table) {
			$table->dropForeign('workers_user_id_foreign');
			$table->dropForeign('workers_restorer_id_foreign');		
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_tva_id_foreign');
			$table->dropForeign('products_restorer_id_foreign');	
			$table->dropForeign('products_category_id_foreign');	
		});
		Schema::table('caching', function(Blueprint $table) {
			$table->dropForeign('caching_order_id_foreign');
		});
	}

}
