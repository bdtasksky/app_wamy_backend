<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccPredefinedTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_predefined')->truncate();
        
        \DB::table('acc_predefined')->insert(array (
            0 => 
            array (
                'id' => 1,
                'predefined_name' => 'Cash',
                'predefined_Description' => 'Assets',
                'is_active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'predefined_name' => 'Bank',
                'predefined_Description' => 'Assets',
                'is_active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'predefined_name' => 'Advance',
                'predefined_Description' => 'Assets',
                'is_active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'predefined_name' => 'Purchase',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'predefined_name' => 'PurchaseDiscount',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'predefined_name' => 'Sales',
                'predefined_Description' => 'Income',
                'is_active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'predefined_name' => 'Sales Discount',
                'predefined_Description' => 'Income',
                'is_active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'predefined_name' => 'Customer',
                'predefined_Description' => 'A/R',
                'is_active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'predefined_name' => 'Supplier',
                'predefined_Description' => 'A/P',
                'is_active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'predefined_name' => 'COGS',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'predefined_name' => 'VAT Payable',
                'predefined_Description' => 'Liability',
                'is_active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'predefined_name' => 'TAX',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'predefined_name' => 'Inventory',
                'predefined_Description' => 'Assets',
                'is_active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'predefined_name' => 'CurrentYearProfitLoss',
                'predefined_Description' => 'O/E',
                'is_active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'predefined_name' => 'LastYearProfitLoss',
                'predefined_Description' => 'O/E',
                'is_active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'predefined_name' => 'Salary',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'predefined_name' => 'VAT Receivable',
                'predefined_Description' => 'Assets',
                'is_active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'predefined_name' => 'empr_npf_contribution',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            18 => 
            array (
                'id' => 19,
                'predefined_name' => 'emp_icf_contribution	',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            19 => 
            array (
                'id' => 20,
                'predefined_name' => 'empr_icf_contribution	',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            20 => 
            array (
                'id' => 21,
                'predefined_name' => 'advance_employee',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            21 => 
            array (
                'id' => 22,
                'predefined_name' => 'EmployeeLoad',
                'predefined_Description' => 'Assets',
                'is_active' => 0,
            ),
            22 => 
            array (
                'id' => 23,
                'predefined_name' => 'Inventory Adjustment',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'predefined_name' => 'cashintransit	',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            24 => 
            array (
                'id' => 25,
                'predefined_name' => 'cardterminal',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            25 => 
            array (
                'id' => 26,
                'predefined_name' => 'product_received_from_ho',
                'predefined_Description' => '',
                'is_active' => 0,
            ),
            26 => 
            array (
                'id' => 27,
                'predefined_name' => 'Service',
                'predefined_Description' => 'Income',
                'is_active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'predefined_name' => 'Sales Return',
                'predefined_Description' => 'Income',
                'is_active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'predefined_name' => 'Purchases Return',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'predefined_name' => 'Transports Cost Purchases',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'predefined_name' => 'Labour Costs Purchases',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'predefined_name' => 'Others Cost Purchases',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'predefined_name' => 'Service Return',
                'predefined_Description' => 'Income',
                'is_active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'predefined_name' => 'Commission Expense',
                'predefined_Description' => 'Exp',
                'is_active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'predefined_name' => 'Commission Payable',
                'predefined_Description' => 'Liability',
                'is_active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'predefined_name' => 'Capital',
                'predefined_Description' => 'OE',
                'is_active' => 1,
            ),
        ));

        
    }
}