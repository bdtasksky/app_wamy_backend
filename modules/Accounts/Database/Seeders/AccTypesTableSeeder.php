<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_types')->truncate();
        
        \DB::table('acc_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_type_name' => 'Asset',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-09-23 15:34:59',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'account_type_name' => 'Expense',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-09-23 15:34:59',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'account_type_name' => 'Income',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-09-23 15:34:59',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'account_type_name' => 'Liability',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-09-23 15:34:59',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'account_type_name' => 'Equity',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-09-23 15:34:59',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));

        
    }
}