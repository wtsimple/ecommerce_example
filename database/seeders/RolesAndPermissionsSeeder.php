<?php


namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create or update permissions
        foreach (Role::CAPABILITIES as $role => $caps) {
            $role = Role::updateOrCreate(['name' => $role]);
            foreach ($caps as $cap) {
                Permission::updateOrCreate(['name' => $cap]);
                $role->givePermissionTo([$cap]);
            }
        }
    }
}
