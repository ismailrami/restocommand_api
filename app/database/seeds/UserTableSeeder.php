<?php 
	class UserTableSeeder extends Seeder 
    {
     	public function run()
        {
    
        $user1 = new User;
        $user1->fill(array(
                    'email'    => 'superadmin@admin.com',
                    ));
        $user1->password = Hash::make('admin');
        $user1->save();
        $role1=Role::find(1);
        $user1->roles()->attach($role1);
        $user1->save();
       }
    }