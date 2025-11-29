<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccFinancialyearTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_financialyear')->truncate();
        
        \DB::table('acc_financialyear')->insert(array (
            0 => 
            array (
                'fiyear_id' => 1,
                'title' => 'Last Year',
                'start_date' => '2024-06-30',
                'end_date' => '2024-06-30',
                'date_time' => '2024-11-24 15:23:50',
                'is_active' => '0',
                'create_by' => '1',
            ),
            1 => 
            array (
                'fiyear_id' => 2,
                'title' => '2024',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-01',
                'date_time' => '2024-11-24 15:23:56',
                'is_active' => '1',
                'create_by' => '39',
            ),
        ));

        
    }
}