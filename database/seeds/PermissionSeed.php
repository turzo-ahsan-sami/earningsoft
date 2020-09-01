<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('cache:clear');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $defaultPermissions = ['users_manage', 'products_manage', 'planTypes_manage', 'modules_manage', 'frontend_manage', 'companies_manage', 'banners_manage', 'discounts_manage', 'admin_login', 'accounting_access', 'admin_home', 'trainings_manage'];

        foreach ($defaultPermissions as $key => $item) {
            Permission::create(
                ['name' => $item]
            );
        }

    }
}
