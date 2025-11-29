<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;

class AccVoucherEventTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('acc_voucher_event')->truncate();
        
        \DB::table('acc_voucher_event')->insert(array (
            0 => 
            array (
                'voucher_event_code' => 'ACC',
                'voucher_event_description' => 'Accounting Module Voucher',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'voucher_event_code' => 'DPMP',
                'voucher_event_description' => 'Due Payment Method FOR Purchases',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'voucher_event_code' => 'DPMP-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'voucher_event_code' => 'DPMP-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'voucher_event_code' => 'DPMPC',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Carrying cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'voucher_event_code' => 'DPMPC-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Carrying cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'voucher_event_code' => 'DPMPC-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Carrying cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'voucher_event_code' => 'DPMPCO',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'voucher_event_code' => 'DPMPCO-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Other cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'voucher_event_code' => 'DPMPCO-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Other cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'voucher_event_code' => 'DPMPCOV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'voucher_event_code' => 'DPMPCOV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying Other cost AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'voucher_event_code' => 'DPMPCOV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying Other cost AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'voucher_event_code' => 'DPMPCV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'voucher_event_code' => 'DPMPCV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'voucher_event_code' => 'DPMPCV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Carrying AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'voucher_event_code' => 'DPMPL',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'voucher_event_code' => 'DPMPL-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'voucher_event_code' => 'DPMPL-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'voucher_event_code' => 'DPMPLC',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND  Carrying cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'voucher_event_code' => 'DPMPLC-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND  Carrying cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'voucher_event_code' => 'DPMPLC-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND  Carrying cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'voucher_event_code' => 'DPMPLCO',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'voucher_event_code' => 'DPMPLCO-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND Other cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'voucher_event_code' => 'DPMPLCO-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND Other cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'voucher_event_code' => 'DPMPLCOV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'voucher_event_code' => 'DPMPLCOV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying Other cost AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'voucher_event_code' => 'DPMPLCOV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying Other cost AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'voucher_event_code' => 'DPMPLCV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND  Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'voucher_event_code' => 'DPMPLCV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND  Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'voucher_event_code' => 'DPMPLCV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Carrying AND  Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'voucher_event_code' => 'DPMPLO',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'voucher_event_code' => 'DPMPLO-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND Other cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'voucher_event_code' => 'DPMPLO-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor AND Other cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'voucher_event_code' => 'DPMPLOV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'voucher_event_code' => 'DPMPLOV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Other cost AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'voucher_event_code' => 'DPMPLOV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor Other cost AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'voucher_event_code' => 'DPMPLV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'voucher_event_code' => 'DPMPLV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'voucher_event_code' => 'DPMPLV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Labor cost AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'voucher_event_code' => 'DPMPO',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'voucher_event_code' => 'DPMPO-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Other cost - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'voucher_event_code' => 'DPMPO-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Other cost - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'voucher_event_code' => 'DPMPOV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'voucher_event_code' => 'DPMPOV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Other cost AND Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'voucher_event_code' => 'DPMPOV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH  Other cost AND Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'voucher_event_code' => 'DPMPV',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            47 => 
            array (
                'voucher_event_code' => 'DPMPV-SPM',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Vat - Single Payment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            48 => 
            array (
                'voucher_event_code' => 'DPMPV-SPMD',
                'voucher_event_description' => 'Due Payment Method FOR Purchases WITH Vat - Single Payment With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            49 => 
            array (
                'voucher_event_code' => 'DPMS',
                'voucher_event_description' => 'Due Payment Method for Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            50 => 
            array (
                'voucher_event_code' => 'DPMS-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            51 => 
            array (
                'voucher_event_code' => 'DPMS-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            52 => 
            array (
                'voucher_event_code' => 'DPMS-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            53 => 
            array (
                'voucher_event_code' => 'DPMS-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            54 => 
            array (
                'voucher_event_code' => 'DPMSS',
                'voucher_event_description' => 'Due Payment Method for Sales with Service',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            55 => 
            array (
                'voucher_event_code' => 'DPMSS-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Service',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            56 => 
            array (
                'voucher_event_code' => 'DPMSS-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Service With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            57 => 
            array (
                'voucher_event_code' => 'DPMSS-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Service',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            58 => 
            array (
                'voucher_event_code' => 'DPMSS-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Service With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            59 => 
            array (
                'voucher_event_code' => 'DPMSSV',
                'voucher_event_description' => 'Due Payment Method for Sales With Service & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            60 => 
            array (
                'voucher_event_code' => 'DPMSSV-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales With Service & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            61 => 
            array (
                'voucher_event_code' => 'DPMSSV-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales With Service & Vat With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            62 => 
            array (
                'voucher_event_code' => 'DPMSSV-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales With Service & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            63 => 
            array (
                'voucher_event_code' => 'DPMSSV-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales With Service & Vat With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            64 => 
            array (
                'voucher_event_code' => 'DPMSSVI',
                'voucher_event_description' => 'Due Payment Method for Sales With Service & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            65 => 
            array (
                'voucher_event_code' => 'DPMSSVI-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales With Service & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            66 => 
            array (
                'voucher_event_code' => 'DPMSSVI-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales With Service & Vat Inclusive With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            67 => 
            array (
                'voucher_event_code' => 'DPMSSVI-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales With Service & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            68 => 
            array (
                'voucher_event_code' => 'DPMSSVI-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales With Service & Vat Inclusive With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            69 => 
            array (
                'voucher_event_code' => 'DPMSV',
                'voucher_event_description' => 'Due Payment Method for Sales with Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            70 => 
            array (
                'voucher_event_code' => 'DPMSV-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            71 => 
            array (
                'voucher_event_code' => 'DPMSV-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Vat With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            72 => 
            array (
                'voucher_event_code' => 'DPMSV-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            73 => 
            array (
                'voucher_event_code' => 'DPMSV-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Vat With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            74 => 
            array (
                'voucher_event_code' => 'DPMSVI',
                'voucher_event_description' => 'Due Payment Method for Sales with Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            75 => 
            array (
                'voucher_event_code' => 'DPMSVI-MPM',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            76 => 
            array (
                'voucher_event_code' => 'DPMSVI-MPMD',
                'voucher_event_description' => 'Due Multiple Payment Method for Sales with Vat Inclusive With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            77 => 
            array (
                'voucher_event_code' => 'DPMSVI-SPM',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            78 => 
            array (
                'voucher_event_code' => 'DPMSVI-SPMD',
                'voucher_event_description' => 'Due Single Payment Method for Sales with Vat Inclusive With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            79 => 
            array (
                'voucher_event_code' => 'INVDEC',
                'voucher_event_description' => 'Inventory Decrease',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            80 => 
            array (
                'voucher_event_code' => 'INVINC',
                'voucher_event_description' => 'Inventory Increase',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            81 => 
            array (
                'voucher_event_code' => 'MPMS',
                'voucher_event_description' => 'Multiple Payment Method for Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            82 => 
            array (
                'voucher_event_code' => 'MPMS-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            83 => 
            array (
                'voucher_event_code' => 'MPMSD',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            84 => 
            array (
                'voucher_event_code' => 'MPMSD-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Discount-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            85 => 
            array (
                'voucher_event_code' => 'MPMSDV',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Discount & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            86 => 
            array (
                'voucher_event_code' => 'MPMSDV-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Discount & Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            87 => 
            array (
                'voucher_event_code' => 'MPMSDVI',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Discount & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            88 => 
            array (
                'voucher_event_code' => 'MPMSDVI-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Discount & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            89 => 
            array (
                'voucher_event_code' => 'MPMSS',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Service',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            90 => 
            array (
                'voucher_event_code' => 'MPMSS-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Service-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            91 => 
            array (
                'voucher_event_code' => 'MPMSSD',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            92 => 
            array (
                'voucher_event_code' => 'MPMSSD-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            93 => 
            array (
                'voucher_event_code' => 'MPMSSDV',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            94 => 
            array (
                'voucher_event_code' => 'MPMSSDV-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount & Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            95 => 
            array (
                'voucher_event_code' => 'MPMSSDVI',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            96 => 
            array (
                'voucher_event_code' => 'MPMSSDVI-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Discount & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            97 => 
            array (
                'voucher_event_code' => 'MPMSSV',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            98 => 
            array (
                'voucher_event_code' => 'MPMSSV-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            99 => 
            array (
                'voucher_event_code' => 'MPMSSVI',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            100 => 
            array (
                'voucher_event_code' => 'MPMSSVI-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales With Service & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            101 => 
            array (
                'voucher_event_code' => 'MPMSV',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            102 => 
            array (
                'voucher_event_code' => 'MPMSV-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            103 => 
            array (
                'voucher_event_code' => 'MPMSVI',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            104 => 
            array (
                'voucher_event_code' => 'MPMSVI-SRA',
                'voucher_event_description' => 'Multiple Payment Method for Sales with Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            105 => 
            array (
                'voucher_event_code' => 'SALESSPLIT',
                'voucher_event_description' => 'SPLIT Payment Method FOR Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            106 => 
            array (
                'voucher_event_code' => 'SPMP',
                'voucher_event_description' => 'Single Payment Method FOR Purchases',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            107 => 
            array (
                'voucher_event_code' => 'SPMPC',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Carrying cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            108 => 
            array (
                'voucher_event_code' => 'SPMPCD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Carrying cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            109 => 
            array (
                'voucher_event_code' => 'SPMPCO',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            110 => 
            array (
                'voucher_event_code' => 'SPMPCOD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying AND Other cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            111 => 
            array (
                'voucher_event_code' => 'SPMPCOV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            112 => 
            array (
                'voucher_event_code' => 'SPMPCOVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying Other cost AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            113 => 
            array (
                'voucher_event_code' => 'SPMPCV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            114 => 
            array (
                'voucher_event_code' => 'SPMPCVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Carrying AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            115 => 
            array (
                'voucher_event_code' => 'SPMPD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            116 => 
            array (
                'voucher_event_code' => 'SPMPL',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            117 => 
            array (
                'voucher_event_code' => 'SPMPLC',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor AND  Carrying cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            118 => 
            array (
                'voucher_event_code' => 'SPMPLCD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor AND  Carrying cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            119 => 
            array (
                'voucher_event_code' => 'SPMPLCO',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            120 => 
            array (
                'voucher_event_code' => 'SPMPLCOD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying AND Other cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            121 => 
            array (
                'voucher_event_code' => 'SPMPLCOV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            122 => 
            array (
                'voucher_event_code' => 'SPMPLCOVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying Other cost AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            123 => 
            array (
                'voucher_event_code' => 'SPMPLCV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying AND  Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            124 => 
            array (
                'voucher_event_code' => 'SPMPLCVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Carrying AND  Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            125 => 
            array (
                'voucher_event_code' => 'SPMPLD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            126 => 
            array (
                'voucher_event_code' => 'SPMPLO',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor AND Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            127 => 
            array (
                'voucher_event_code' => 'SPMPLOD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor AND Other cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            128 => 
            array (
                'voucher_event_code' => 'SPMPLOV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            129 => 
            array (
                'voucher_event_code' => 'SPMPLOVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor Other cost AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            130 => 
            array (
                'voucher_event_code' => 'SPMPLV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            131 => 
            array (
                'voucher_event_code' => 'SPMPLVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Labor cost AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            132 => 
            array (
                'voucher_event_code' => 'SPMPO',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Other cost',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            133 => 
            array (
                'voucher_event_code' => 'SPMPOD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Other cost Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            134 => 
            array (
                'voucher_event_code' => 'SPMPOV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Other cost AND Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            135 => 
            array (
                'voucher_event_code' => 'SPMPOVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH  Other cost AND Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            136 => 
            array (
                'voucher_event_code' => 'SPMPRF',
                'voucher_event_description' => 'Single Payment Method FOR Purchases Return WITH Full Adjust',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            137 => 
            array (
                'voucher_event_code' => 'SPMPV',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            138 => 
            array (
                'voucher_event_code' => 'SPMPVD',
                'voucher_event_description' => 'Single Payment Method FOR Purchases WITH Vat Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            139 => 
            array (
                'voucher_event_code' => 'SPMS',
                'voucher_event_description' => 'Single Payment method For Sales',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            140 => 
            array (
                'voucher_event_code' => 'SPMS-SRA',
                'voucher_event_description' => 'Single Payment method For Sales-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            141 => 
            array (
                'voucher_event_code' => 'SPMSD',
                'voucher_event_description' => 'Single Payment method For Sales With Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            142 => 
            array (
                'voucher_event_code' => 'SPMSD-SRA',
                'voucher_event_description' => 'Single Payment method For Sales With Discount-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            143 => 
            array (
                'voucher_event_code' => 'SPMSDV',
                'voucher_event_description' => 'Single Payment method For Sales With Discount And Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            144 => 
            array (
                'voucher_event_code' => 'SPMSDV-SRA',
                'voucher_event_description' => 'Single Payment method For Sales With Discount And Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            145 => 
            array (
                'voucher_event_code' => 'SPMSDVI',
                'voucher_event_description' => 'Single Payment Method for Sales With Discount & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            146 => 
            array (
                'voucher_event_code' => 'SPMSDVI-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Discount & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            147 => 
            array (
                'voucher_event_code' => 'SPMSRA',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Replays Adjust',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            148 => 
            array (
                'voucher_event_code' => 'SPMSRA-SRA',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Replays Adjust-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            149 => 
            array (
                'voucher_event_code' => 'SPMSRF',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Full Adjust',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            150 => 
            array (
                'voucher_event_code' => 'SPMSRF-SRA',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Full Adjust-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            151 => 
            array (
                'voucher_event_code' => 'SPMSRP',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Partial Adjust',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            152 => 
            array (
                'voucher_event_code' => 'SPMSRP-SRA',
                'voucher_event_description' => 'Single Payment Method FOR Sales Return WITH Partial Adjust-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            153 => 
            array (
                'voucher_event_code' => 'SPMSS',
                'voucher_event_description' => 'Single Payment Method for Sales with Service',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            154 => 
            array (
                'voucher_event_code' => 'SPMSS-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales with Service-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            155 => 
            array (
                'voucher_event_code' => 'SPMSSD',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            156 => 
            array (
                'voucher_event_code' => 'SPMSSD-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            157 => 
            array (
                'voucher_event_code' => 'SPMSSDV',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            158 => 
            array (
                'voucher_event_code' => 'SPMSSDV-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount & Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            159 => 
            array (
                'voucher_event_code' => 'SPMSSDVI',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            160 => 
            array (
                'voucher_event_code' => 'SPMSSDVI-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Discount & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            161 => 
            array (
                'voucher_event_code' => 'SPMSSV',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Vat',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            162 => 
            array (
                'voucher_event_code' => 'SPMSSV-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Vat-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            163 => 
            array (
                'voucher_event_code' => 'SPMSSVI',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            164 => 
            array (
                'voucher_event_code' => 'SPMSSVI-SRA',
                'voucher_event_description' => 'Single Payment Method for Sales With Service & Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            165 => 
            array (
                'voucher_event_code' => 'SPMSV',
                'voucher_event_description' => 'Single Payment method For Sales With VAT',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            166 => 
            array (
                'voucher_event_code' => 'SPMSV-SRA',
                'voucher_event_description' => 'Single Payment method For Sales With VAT-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            167 => 
            array (
                'voucher_event_code' => 'SPMSVI',
                'voucher_event_description' => 'Single Payment Method FOR Sales WITH Vat Inclusive',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            168 => 
            array (
                'voucher_event_code' => 'SPMSVI-SRA',
                'voucher_event_description' => 'Single Payment Method FOR Sales WITH Vat Inclusive-Sales Replacement or Adjustment',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '1970-01-01 01:01:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));

        
    }
}