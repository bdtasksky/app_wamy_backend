<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccSubtypeTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_subtype')->truncate();
        
        \DB::table('acc_subtype')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'None',
                'isSystem' => 1,
                'code' => '1',
                'created_by' => 2,
                'created_date' => '2023-05-30 03:57:39',
                'updated_by' => 2,
                'updated_date' => '2023-04-10 04:41:36',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Employee',
                'isSystem' => 1,
                'code' => '2',
                'created_by' => 2,
                'created_date' => '2023-05-30 03:57:43',
                'updated_by' => 2,
                'updated_date' => '2023-04-10 04:41:28',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Customer',
                'isSystem' => 1,
                'code' => '3',
                'created_by' => 2,
                'created_date' => '2023-05-30 03:57:46',
                'updated_by' => 2,
                'updated_date' => '2023-04-10 04:41:21',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Supplier',
                'isSystem' => 1,
                'code' => '4',
                'created_by' => 2,
                'created_date' => '2023-05-30 03:57:49',
                'updated_by' => 2,
                'updated_date' => '2023-04-10 04:41:13',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CARS',
                'isSystem' => 0,
                'code' => '5',
                'created_by' => 2,
                'created_date' => '2023-05-30 03:57:53',
                'updated_by' => 2,
                'updated_date' => '2023-04-10 04:44:20',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Delivery Agent',
                'isSystem' => 1,
                'code' => 'da',
                'created_by' => 2,
                'created_date' => '2023-05-08 23:31:45',
                'updated_by' => 2,
                'updated_date' => '2023-05-08 23:31:45',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'capital',
                'isSystem' => 1,
                'code' => '1231',
                'created_by' => 1,
                'created_date' => '2024-10-23 18:39:06',
                'updated_by' => 1,
                'updated_date' => '2024-10-23 18:39:06',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'Test M ST',
                'isSystem' => 1,
                'code' => NULL,
                'created_by' => 0,
                'created_date' => '2024-12-23 19:06:22',
                'updated_by' => 0,
                'updated_date' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));

        
    }
}