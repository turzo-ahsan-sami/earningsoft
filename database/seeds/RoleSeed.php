<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'administrator']);

        $permissions = Permission::all();

        foreach ($permissions as $key => $item) {
            $role->givePermissionTo($item->name);
        }
        
    }
}
