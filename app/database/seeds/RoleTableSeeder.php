<?php 
	class RoleTableSeeder extends Seeder {
     	public function run(){
         	DB::table('roles')->delete();
         	
         	Role::create(array(
             'role' => 'superadmin',
         ));
         	Role::create(array(
             'role' => 'admin',
         ));
            Role::create(array(
             'role' => 'Cuisinier',
         ));
            Role::create(array(
             'role' => 'Serveur',
         ));
            Role::create(array(
             'role' => 'Caissier',
         ));
       }
     }