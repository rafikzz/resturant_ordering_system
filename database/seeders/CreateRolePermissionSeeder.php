<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role_list',
            'role_create',
            'role_edit',
            'role_delete',
            'user_list',
            'user_create',
            'user_edit',
            'user_delete',
            'item_list',
            'item_create',
            'item_edit',
            'item_delete',
            'category_list',
            'category_create',
            'category_edit',
            'category_delete',
            'customer_list',
            'customer_create',
            'customer_edit',
            'customer_delete',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
            'report_list',
            'setting_create',
         ];

         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
         $adminPermmissions= [
            'item_list',
            'item_create',
            'item_edit',
            'item_delete',
            'category_list',
            'category_create',
            'category_edit',
            'category_delete',
            'customer_list',
            'customer_create',
            'customer_edit',
            'customer_delete',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
            'report_list',
            'setting_create',
         ];
         $staffPermission= [
            'item_list',
            'item_create',
            'item_edit',
            'item_delete',
            'category_list',
            'category_create',
            'category_edit',
            'category_delete',
            'customer_list',
            'customer_create',
            'customer_edit',
            'customer_delete',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
         ];

        Role::create(['name' => 'Superadmin']);
        $roleAdmin =Role::create(['name' => 'Admin']);
        $roleAdmin->givePermissionTo($adminPermmissions);
        $roleStaff =Role::create(['name' => 'Staff']);
        $roleStaff->givePermissionTo($staffPermission);



    }
}
