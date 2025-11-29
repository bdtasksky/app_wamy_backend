<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccVouchartypeTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_vouchartype')->truncate();
        
        \DB::table('acc_vouchartype')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Debit Vouchar',
                'PrefixCode' => 'DV',
                'isauto' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Credit Vouchar',
                'PrefixCode' => 'CV',
                'isauto' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Journal Vouchar',
                'PrefixCode' => 'JV',
                'isauto' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Contra Vouchar',
                'PrefixCode' => 'TV',
                'isauto' => 1,
            ),
        ));

        
    }
}