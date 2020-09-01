<?php

use Illuminate\Database\Seeder;
use App\Admin\Company;

class CompanySeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */


    public function run()
    {
        $deafultCompanies = [
            [
                'name' => 'Earning Soft',
                'email' => 'info@earningsoft.com',
                'mobile' => '01781334567',
                'address' => 'Shyamoli, Dhaka-1207, Bangladesh',
                'website' => 'www.earningsoft.com',
                'logo' => '001',
            ],

        ];

        foreach ($deafultCompanies as $key => $company) {
            Company::create($company);
        }

    }
}
