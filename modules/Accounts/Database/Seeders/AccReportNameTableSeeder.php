<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccReportNameTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_report_name')->truncate();
        
        \DB::table('acc_report_name')->insert(array (
            0 => 
            array (
                'id' => 1,
                'report_name' => 'Income Statement',
                'remarks' => 'This report provides details on income and expenses.',
                'is_active' => 1,
                'created_at' => '2024-12-01 13:04:51',
                'updated_at' => NULL,
            ),
        ));

        
    }
}