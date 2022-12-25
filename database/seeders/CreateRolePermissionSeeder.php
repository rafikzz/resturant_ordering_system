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
            'category_list',
            'category_create',
            'category_edit',
            'category_delete',
            'item_list',
            'item_create',
            'item_edit',
            'item_delete',
            'customer_list',
            'customer_create',
            'customer_edit',
            'patient_list',
            'patient_create',
            'patient_edit',
            'patient_discharge',
            'staff_list',
            'staff_create',
            'staff_edit',
            'staff_wallet_transaction',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
            'order_add',
            'order_breakdown',
            'checkout_edit',
            'report_list',
            'setting_create',
            'coupon_list',
            'coupon_create',
            'coupon_edit',
            'coupon_delete',
            'status_list',
            'status_edit',
            'department_list',
            'department_create',
            'department_edit',
            'department_delete',
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
            'patient_list',
            'patient_create',
            'patient_edit',
            'patient_discharge',
            'staff_list',
            'staff_create',
            'staff_edit',
            'staff_wallet_transaction',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
            'order_add',
            'order_breakdown',
            'report_list',
            'setting_create',
            'coupon_list',
            'coupon_create',
            'coupon_edit',
            'coupon_delete',
         ];
         $staffPermission= [
            'item_list',
            'item_create',
            'item_edit',
            'item_delete',
            'patient_list',
            'patient_create',
            'patient_edit',
            'patient_discharge',
            'staff_list',
            'staff_create',
            'staff_edit',
            'staff_wallet_transaction',
            'order_list',
            'order_create',
            'order_edit',
            'order_delete',
            'order_add',
            'order_breakdown',
            'coupon_list',
            'coupon_create',
            'coupon_edit',
            'coupon_delete',

         ];

        Role::create(['name' => 'Superadmin']);
        $roleAdmin =Role::create(['name' => 'Admin']);
        $roleAdmin->givePermissionTo($adminPermmissions);
        $roleStaff =Role::create(['name' => 'Staff']);
        $roleStaff->givePermissionTo($staffPermission);



    }
}
