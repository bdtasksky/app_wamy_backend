<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccOpeningbalanceTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_openingbalance')->truncate();
        
        \DB::table('acc_openingbalance')->insert(array (
            0 => 
            array (
                'id' => 25,
                'financial_year_id' => 1,
                'acc_coa_id' => 57,
                'account_code' => NULL,
                'debit' => '100.000',
                'credit' => '0.000',
                'open_date' => '2024-06-30',
                'acc_subtype_id' => NULL,
                'acc_subcode_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 26,
                'financial_year_id' => 1,
                'acc_coa_id' => 118,
                'account_code' => NULL,
                'debit' => '0.000',
                'credit' => '100.000',
                'open_date' => '2024-06-30',
                'acc_subtype_id' => NULL,
                'acc_subcode_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));

        
    }
}