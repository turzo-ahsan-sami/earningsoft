<?php

use Illuminate\Database\Seeder;
use App\Admin\PlanType;

class PlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deafultPlanTypes = [
            [
                'name' => 'Day',
                'slug' => 'day',
            ],
            [
                'name' => 'Week',
                'slug' => 'week',
            ],
            [
                'name' => 'Month',
                'slug' => 'month',
            ],
            [
                'name' => 'Year',
                'slug' => 'year',
            ]
        ];

        foreach ($deafultPlanTypes as $key => $planType) {
            PlanType::create($planType);
        }

    }
}
