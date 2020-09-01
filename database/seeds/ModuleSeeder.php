<?php

use Illuminate\Database\Seeder;
use App\Admin\Module;

class ModuleSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */


    public function run()
    {
        $deafultModules = [
            [
                'name' => 'Accounting',
                'slug' => 'acc',
                'code' => '001',

            ],
            [
                'name' => 'Inventory',
                'slug' => 'inv',
                'code' => '002',

            ],
            [
                'name' => 'Fixed Asset Management',
                'slug' => 'fams',
                'code' => '003',

            ],
            [
                'name' => 'Billing',
                'slug' => 'bil',
                'code' => '004',

            ]
        ];

        foreach ($deafultModules as $key => $module) {
            Module::create($module);
        }

    }
}
