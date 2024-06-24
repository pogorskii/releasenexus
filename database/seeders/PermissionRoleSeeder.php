<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect([
            [
                'name'        => 'super-admin',
                'permissions' => [
                    'manage users',
                    'manage partners',
                    'manage companies',
                    'manage case studies',
                ],
            ],
            [
                'name'        => 'admin',
                'permissions' => [
                    'manage partners',
                ],
            ],
            [
                'name'        => 'user',
                'permissions' => [
                    'view partners',
                    'view companies',
                ],
            ],
        ]);

        $roles->each(function ($r) {
            $role = Role::firstOrCreate(['name' => $r['name']]);
            foreach ($r['permissions'] as $p) {
                try {
                    Permission::create(['name' => $p]);
                } catch (PermissionAlreadyExists) {
                    // Do nothing
                }
                $role->givePermissionTo($p);
            }
        });

        $permissions = collect([
            'manage users',
            'manage partners',
            'manage companies',
            'manage case studies',
            'view partners',
            'view companies',
        ]);

        $permissions->each(function ($p) {
            try {
                Permission::create(['name' => $p]);
            } catch (PermissionAlreadyExists) {
                // Do nothing
            }
        });
    }
}
