<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class UserPermissionSeeder extends Seeder
{
    const NEED_FIRST_USER = true;
    const ADMIN_PANEL_PERMISSION = 'access_admin_panel';
    const PERMISSIONS = [
        self::ADMIN_PANEL_PERMISSION,
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->createPermissions();
        $this->createRoles(self::NEED_FIRST_USER);
    }

    private function createPermissions()
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    private function createRoles(bool $needFirstUser = true)
    {
        // Admin
        $adminRole = Role::create(['name' => 'admin']);
        foreach (self::PERMISSIONS as $permission) {
            $adminRole->givePermissionTo($permission);
        }

        // Manager
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(self::ADMIN_PANEL_PERMISSION);


        if($needFirstUser) {
            $this->createFirstUser($adminRole);
        }
    }

    private function createFirstUser(Role $role)
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@localhost',
            'password' => Hash::make('1234'),
        ]);
        $admin->assignRole($role);
    }
}
