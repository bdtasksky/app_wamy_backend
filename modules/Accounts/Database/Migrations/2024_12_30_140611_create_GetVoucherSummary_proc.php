<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE `GetVoucherSummary`(IN `p_StartDate` DATE, IN `p_EndDate` DATE)
BEGIN
    
    SELECT t.name AS VoucherName, COUNT(*) AS VoucherNumber
    FROM acc_voucher_master m
    INNER JOIN acc_vouchartype t ON m.VoucharTypeId = t.id
    WHERE m.VoucherDate BETWEEN p_StartDate AND p_EndDate
    AND m.IsApprove = 1
    GROUP BY t.id

    UNION ALL

    
    SELECT 'Unapproved Voucher', COUNT(*)
    FROM acc_voucher_master m
    WHERE m.VoucherDate BETWEEN p_StartDate AND p_EndDate
    AND m.IsApprove = 0

    UNION ALL

    
    SELECT 'Total Voucher', COUNT(*)
    FROM acc_voucher_master m
    WHERE m.VoucherDate BETWEEN p_StartDate AND p_EndDate;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetVoucherSummary");
    }
};
