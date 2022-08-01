<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);
        Permission::create(['name' => 'unpublish articles']);

        // create roles and assign existing permissions
        $admin_role = Role::create(['name' => 'Super-Admin']);
        $admin_role->givePermissionTo('edit articles');
        $admin_role->givePermissionTo('delete articles');
        $admin_role->givePermissionTo('publish articles');
        $admin_role->givePermissionTo('unpublish articles');

        $user_role = Role::create(['name' => 'User']);
        $user_role->givePermissionTo('edit articles');
        $user_role->givePermissionTo('delete articles');
        $user_role->givePermissionTo('publish articles');
        $user_role->givePermissionTo('unpublish articles');

        // create demo users
        $admin_user = User::create([
            'name' => 'zaroor',
            'email' => 'zaroon@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $admin_user->assignRole($admin_role);

        $user1 = User::create([
            'name' => 'user',
            'email' => 'user1@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $user2 = User::create([
            'name' => 'user',
            'email' => 'user2@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $user3 = User::create([
            'name' => 'user',
            'email' => 'user3@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $user4 = User::create([
            'name' => 'user',
            'email' => 'user4@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $user1->assignRole($user_role);
        $user2->assignRole($user_role);
        $user3->assignRole($user_role);
        $user4->assignRole($user_role);
    }
}
