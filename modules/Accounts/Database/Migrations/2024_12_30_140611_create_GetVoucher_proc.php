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
        DB::unprepared("CREATE PROCEDURE `GetVoucher`(IN `p_voucher_id` INT, IN `p_voucher_date` DATE)
BEGIN
    DECLARE l_previous_fiyear_id INT;
    DECLARE l_current_fiyear_id INT;
    DECLARE l_current_fy_status INT;

    
    SELECT fiyear_id, is_active
    INTO l_current_fiyear_id, l_current_fy_status
    FROM acc_financialyear f
    WHERE f.start_date <= p_voucher_date
    ORDER BY f.start_date DESC
    LIMIT 1;

    
    IF l_current_fy_status = 1 THEN    
        SELECT 
            m.id, 
            m.VoucherNumber,
            m.VoucherDate,
            m.Remarks, 
            m.TranAmount, 
            CASE 
                WHEN m.IsApprove = 1 THEN 'Approved Voucher' 
                ELSE 'Pending Voucher' 
            END AS ApprovedStatus,
            m.IsApprove,
            t.name AS VoucherType,
            f.title AS Fiyear_name,
            CONCAT(
                a.account_name,
                CASE 
                    WHEN s.name = 'None' OR s.name IS NULL THEN '' 
                    ELSE CONCAT(' (', s.name, ')')
                END
            ) AS account_name,
            COALESCE(d.LaserComments, '') AS LaserComments,
            d.Dr_Amount,
            d.Cr_Amount
        FROM 
            acc_voucher_master m 
        INNER JOIN 
            acc_voucher_details d ON m.id = d.voucher_master_id 
        INNER JOIN 
            acc_vouchartype t ON t.id = m.VoucharTypeId 
        INNER JOIN 
            acc_financialyear f ON f.fiyear_id = m.FinancialYearId
        INNER JOIN 
            acc_coas a ON a.id = d.acc_coa_id
        LEFT JOIN 
            acc_subcode s ON s.id = d.subcode_id
        WHERE 
            m.id = p_voucher_id AND m.VoucherDate = p_voucher_date;

    ELSEIF l_current_fy_status = 2 THEN    
        SET @sql_query = CONCAT(
            'SELECT 
                m.id, 
                m.VoucherNumber,
                m.VoucherDate,
                m.Remarks, 
                m.TranAmount, 
                CASE 
                    WHEN m.IsApprove = 1 THEN \"Approved Voucher\" 
                    ELSE \"Pending Voucher\" 
                END AS ApprovedStatus,
                m.IsApprove,
                t.name AS VoucherType,
                f.title AS Fiyear_name,
                CONCAT(
                    a.account_name,
                    CASE 
                        WHEN s.name = \"NONE\" OR s.name IS NULL THEN \"\" 
                        ELSE CONCAT(\" (\", s.name, \")\")
                    END
                ) AS account_name,
                COALESCE(d.LaserComments, \"\") AS LaserComments,
                d.Dr_Amount,
                d.Cr_Amount
            FROM 
                acc_voucher_master', l_current_fiyear_id, ' m 
            INNER JOIN 
                acc_voucher_details', l_current_fiyear_id, ' d ON m.id = d.voucher_master_id 
            INNER JOIN 
                acc_vouchartype t ON t.id = m.VoucharTypeId 
            INNER JOIN 
                acc_financialyear f ON f.fiyear_id = m.FinancialYearId
            INNER JOIN 
                acc_coas a ON a.id = d.acc_coa_id
            LEFT JOIN 
                acc_subcode s ON s.id = d.subcode_id
            WHERE 
                m.id = ', p_voucher_id, ' AND m.VoucherDate = \"', p_voucher_date, '\";'
        );
        
        
        PREPARE stmt FROM @sql_query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
      ELSE
      SELECT 
            m.id, 
            m.VoucherNumber,
            m.VoucherDate,
            m.Remarks, 
            m.TranAmount, 
            CASE 
                WHEN m.IsApprove = 1 THEN 'Approved Voucher' 
                ELSE 'Pending Voucher' 
            END AS ApprovedStatus,
            m.IsApprove,
            t.name AS VoucherType,
            f.title AS Fiyear_name,
            CONCAT(
                a.account_name,
                CASE 
                    WHEN s.name = 'None' OR s.name IS NULL THEN '' 
                    ELSE CONCAT(' (', s.name, ')')
                END
            ) AS account_name,
            COALESCE(d.LaserComments, '') AS LaserComments,
            d.Dr_Amount,
            d.Cr_Amount
        FROM 
            acc_voucher_master m 
        INNER JOIN 
            acc_voucher_details d ON m.id = d.voucher_master_id 
        INNER JOIN 
            acc_vouchartype t ON t.id = m.VoucharTypeId 
        INNER JOIN 
            acc_financialyear f ON f.fiyear_id = m.FinancialYearId
        INNER JOIN 
            acc_coas a ON a.id = d.acc_coa_id
        LEFT JOIN 
            acc_subcode s ON s.id = d.subcode_id
        WHERE 
            m.id = p_voucher_id AND m.VoucherDate = p_voucher_date;
    END IF;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetVoucher");
    }
};
