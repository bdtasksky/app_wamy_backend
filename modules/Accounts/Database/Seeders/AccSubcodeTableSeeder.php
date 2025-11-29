<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccSubcodeTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_subcode')->truncate();
        
        \DB::table('acc_subcode')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'None',
                'subTypeID' => 1,
                'refCode' => '0',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Walkin',
                'subTypeID' => 3,
                'refCode' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Test Supplier',
                'subTypeID' => 4,
                'refCode' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '7ukiik',
                'subTypeID' => 2,
                'refCode' => '',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 7,
                'name' => 'Mishor',
                'subTypeID' => 3,
                'refCode' => '5',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 9,
                'name' => 'Test M SC',
                'subTypeID' => 10,
                'refCode' => '',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 13,
                'name' => 'Test',
                'subTypeID' => 3,
                'refCode' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 14,
                'name' => 'New Customer',
                'subTypeID' => 3,
                'refCode' => '6',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 15,
                'name' => 'Jalal',
                'subTypeID' => 3,
                'refCode' => '7',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 16,
                'name' => 'Salman',
                'subTypeID' => 3,
                'refCode' => '8',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 17,
                'name' => 'Kalam updated',
                'subTypeID' => 3,
                'refCode' => '9',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 18,
                'name' => 'Rami updated',
                'subTypeID' => 3,
                'refCode' => '10',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 19,
                'name' => 'Kerry Arthur Phelps Gay',
                'subTypeID' => 2,
                'refCode' => '',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 20,
                'name' => 'Mridul',
                'subTypeID' => 3,
                'refCode' => '11',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));

        
    }
}