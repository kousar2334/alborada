<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------
        // 1. Create permissions
        // ----------------------------
        $permissions = [
            ['name' => 'User List', 'module' => 'Users'],
            ['name' => 'User Delete', 'module' => 'Users'],
            ['name' => 'Permission List View', 'module' => 'Users'],
            ['name' => 'Role List View', 'module' => 'Users'],
            ['name' => 'Update Environment', 'module' => 'System'],
            ['name' => 'View Dashboard', 'module' => 'Dashboard'],
            ['name' => 'Manage Media', 'module' => 'Media'],
            ['name' => 'Delete Media', 'module' => 'Media'],
            ['name' => 'Manage Pages', 'module' => 'Pages'],
            ['name' => 'Delete Page', 'module' => 'Pages'],
            ['name' => 'Create New Page', 'module' => 'Pages'],
            ['name' => 'Manage Appearances', 'module' => 'Appearances'],
            ['name' => 'Manage Menu', 'module' => 'Appearances'],
            ['name' => 'Delete Menu', 'module' => 'Appearances'],
            ['name' => 'Manage Site Settings', 'module' => 'Appearances'],
            ['name' => 'Manage Category', 'module' => 'Products'],
            ['name' => 'Create New Category', 'module' => 'Products'],
            ['name' => 'Delete Category', 'module' => 'Products'],
            ['name' => 'Manage Product', 'module' => 'Products'],
            ['name' => 'Create New Product', 'module' => 'Products'],
            ['name' => 'Delete Product', 'module' => 'Products'],
            ['name' => 'Manage Product Brochure', 'module' => 'Products'],
            ['name' => 'Manage Language', 'module' => 'Language'],
            ['name' => 'Add Language', 'module' => 'Language'],
            ['name' => 'Delete Language', 'module' => 'Language'],
            ['name' => 'Manage Dealer', 'module' => 'Dealers'],
            ['name' => 'Manage Message', 'module' => 'Messages'],
            ['name' => 'Manage Blog', 'module' => 'Blogs'],
            ['name' => 'Create New Blog', 'module' => 'Blogs'],
            ['name' => 'Manage Blog Category', 'module' => 'Blogs'],
            ['name' => 'Delete Blog Category', 'module' => 'Blogs'],
            ['name' => 'Delete Blog', 'module' => 'Blogs'],
            ['name' => 'Update SMTP', 'module' => 'System'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['module' => $perm['module'], 'guard_name' => 'web']
            );
        }

        // ----------------------------
        // 2. Create Super Admin Role
        // ----------------------------
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['guard_name' => 'web']
        );

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // ----------------------------
        // 3. Create Admin User
        // ----------------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('111111'),
                'type' => 1,
                'status' => 1,
            ]
        );

        // Assign Super Admin Role to user
        if (! $admin->hasRole('Super Admin')) {
            $admin->assignRole($superAdminRole);
        }
    }
}
