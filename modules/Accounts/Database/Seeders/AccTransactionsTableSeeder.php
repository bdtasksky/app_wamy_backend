<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccTransactionsTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_transactions')->truncate();
        
        \DB::table('acc_transactions')->insert(array (
            0 => 
            array (
                'id' => 65,
                'voucher_master_id' => 23,
                'Companyid' => 0,
                'BranchId' => 1,
                'FinancialYearId' => 2,
                'VoucharTypeId' => 2,
                'voucher_event_code' => 'ACC',
                'VoucherNumber' => 'CV_2412_000001',
                'Remarks' => 'CS1',
                'VoucherDate' => '2024-12-23',
                'acc_coa_id' => 53,
                'subtype_id' => NULL,
                'subcode_id' => NULL,
                'cheque_no' => '',
                'cheque_date' => '1900-01-01',
                'is_honour' => 0,
                'ledger_comment' => '',
                'Dr_Amount' => '500.000',
                'Cr_Amount' => '0.000',
                'reverse_acc_coa_id' => 102,
                'PurchaseID' => NULL,
                'BillID' => NULL,
                'ServiceID' => NULL,
                'IsYearClosed' => 0,
                'created_by' => 39,
                'created_date' => '2024-12-23',
                'approved_by' => 39,
                'approved_at' => '2024-12-23 19:15:30',
            ),
            1 => 
            array (
                'id' => 66,
                'voucher_master_id' => 23,
                'Companyid' => 0,
                'BranchId' => 1,
                'FinancialYearId' => 2,
                'VoucharTypeId' => 2,
                'voucher_event_code' => 'ACC',
                'VoucherNumber' => 'CV_2412_000001',
                'Remarks' => 'CS1',
                'VoucherDate' => '2024-12-23',
                'acc_coa_id' => 102,
                'subtype_id' => NULL,
                'subcode_id' => NULL,
                'cheque_no' => '',
                'cheque_date' => '1900-01-01',
                'is_honour' => 0,
                'ledger_comment' => '',
                'Dr_Amount' => '0.000',
                'Cr_Amount' => '500.000',
                'reverse_acc_coa_id' => 53,
                'PurchaseID' => NULL,
                'BillID' => NULL,
                'ServiceID' => NULL,
                'IsYearClosed' => 0,
                'created_by' => 39,
                'created_date' => '2024-12-23',
                'approved_by' => 39,
                'approved_at' => '2024-12-23 19:15:30',
            ),
        ));

        
    }
}