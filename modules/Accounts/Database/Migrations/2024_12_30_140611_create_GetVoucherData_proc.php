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
        DB::unprepared("CREATE PROCEDURE `GetVoucherData`(IN `p_VoucharTypeId` INT, IN `p_IsApprove` INT, IN `p_start_date` DATE, IN `p_end_date` DATE)
BEGIN
    DECLARE l_previous_fiyear_id INT;
    DECLARE l_current_fiyear_id INT;
    DECLARE l_current_fy_status INT;

    
    SELECT fiyear_id, is_active
    INTO l_current_fiyear_id, l_current_fy_status
    FROM acc_financialyear f
    WHERE f.start_date <= p_start_date
    ORDER BY f.start_date DESC
    LIMIT 1;

    
    IF l_current_fy_status = 1 THEN    
        
        IF p_VoucharTypeId = 0 THEN
            IF p_IsApprove = -1 THEN
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date
                GROUP BY 
                    m.id
                HAVING 
                    dr != cr;
            ELSE
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date 
                    AND m.IsApprove = p_IsApprove
                GROUP BY 
                    m.id
                HAVING 
                    dr != cr;
            END IF;
        ELSEIF p_VoucharTypeId = -1 THEN
            
            IF p_IsApprove = -1 THEN
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date
                GROUP BY 
                    m.id;
            ELSE
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date 
                    AND m.IsApprove = p_IsApprove
                GROUP BY 
                    m.id;
            END IF;
        ELSE
            
            IF p_IsApprove = -1 THEN
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date 
                    AND m.VoucharTypeId = p_VoucharTypeId
                GROUP BY 
                    m.id;
            ELSE
                
                SELECT 
                    m.id AS voucher_master_id, 
                    m.VoucharTypeId,
                    m.VoucherNumber,
                    m.VoucherDate,
                    m.Remarks,
                    m.TranAmount,
                    m.IsApprove,
                    SUM(d.Dr_Amount) AS dr,
                    SUM(d.Cr_Amount) AS cr,
                    m.IsYearClosed
                FROM 
                    acc_voucher_master m
                INNER JOIN 
                    acc_voucher_details d ON m.id = d.voucher_master_id
                WHERE 
                    m.VoucherDate BETWEEN p_start_date AND p_end_date 
                    AND m.VoucharTypeId = p_VoucharTypeId 
                    AND m.IsApprove = p_IsApprove
                GROUP BY 
                    m.id;
            END IF;
        END IF;
      ELSE
        
        
	    IF p_VoucharTypeId= -1 THEN
	    
	    
			     
			    IF p_IsApprove= -1 THEN
			    
			    SET @sql_query = CONCAT(
		    'SELECT ',
			'm.id AS voucher_master_id, ',
			'm.VoucharTypeId, ',
			'm.VoucherNumber, ',
			'm.VoucherDate, ',
			'm.Remarks, ',
			'm.TranAmount, ',
			'm.IsApprove, ',
			'SUM(d.Dr_Amount) AS dr, ',
			'SUM(d.Cr_Amount) AS cr, ',
			'm.IsYearClosed ',
		    'FROM ',
			'acc_voucher_master', l_current_fiyear_id, ' m ',
		    'INNER JOIN ',
			'acc_voucher_details', l_current_fiyear_id, ' d ',
		    'ON ',
			'm.id = d.voucher_master_id ',
		    'WHERE ',
			'm.VoucherDate BETWEEN \'', p_start_date, '\' AND \'', p_end_date, '\' ',
		    'GROUP BY ',
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed;'
		);
	       
			    ELSE
			    
			    SET @sql_query = CONCAT(
		    'SELECT ',
			'm.id AS voucher_master_id, ',
			'm.VoucharTypeId, ',
			'm.VoucherNumber, ',
			'm.VoucherDate, ',
			'm.Remarks, ',
			'm.TranAmount, ',
			'm.IsApprove, ',
			'SUM(d.Dr_Amount) AS dr, ',
			'SUM(d.Cr_Amount) AS cr, ',
			'm.IsYearClosed ',
		    'FROM ',
			'acc_voucher_master', l_current_fiyear_id, ' m ',
		    'INNER JOIN ',
			'acc_voucher_details', l_current_fiyear_id, ' d ',
		    'ON ',
			'm.id = d.voucher_master_id ',
		    'WHERE ',
			'm.VoucherDate BETWEEN \'', p_start_date, '\' AND \'', p_end_date, '\' ',
		       'AND m.IsApprove = ', p_IsApprove, ' ',
		    'GROUP BY ',
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed;'
		);
	       
			    END IF;
	    ELSE 
	    
			     
			    IF p_IsApprove= -1 THEN
			    
			   SET @sql_query = CONCAT(
		    'SELECT ',
			'm.id AS voucher_master_id, ',
			'm.VoucharTypeId, ',
			'm.VoucherNumber, ',
			'm.VoucherDate, ',
			'm.Remarks, ',
			'm.TranAmount, ',
			'm.IsApprove, ',
			'SUM(d.Dr_Amount) AS dr, ',
			'SUM(d.Cr_Amount) AS cr, ',
			'm.IsYearClosed ',
		    'FROM ',
			'acc_voucher_master', l_current_fiyear_id, ' m ',
		    'INNER JOIN ',
			'acc_voucher_details', l_current_fiyear_id, ' d ',
		    'ON ',
			'm.id = d.voucher_master_id ',
		    'WHERE ',
			'm.VoucherDate BETWEEN \'', p_start_date, '\' AND \'', p_end_date, '\' ',
		       'AND m.VoucharTypeId = ', p_VoucharTypeId, ' ',
		    'GROUP BY ',
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed;'
		);
	       
			    ELSE
			    
			   SET @sql_query = CONCAT(
		    'SELECT ',
			'm.id AS voucher_master_id, ',
			'm.VoucharTypeId, ',
			'm.VoucherNumber, ',
			'm.VoucherDate, ',
			'm.Remarks, ',
			'm.TranAmount, ',
			'm.IsApprove, ',
			'SUM(d.Dr_Amount) AS dr, ',
			'SUM(d.Cr_Amount) AS cr, ',
			'm.IsYearClosed ',
		    'FROM ',
			'acc_voucher_master', l_current_fiyear_id, ' m ',
		    'INNER JOIN ',
			'acc_voucher_details', l_current_fiyear_id, ' d ',
		    'ON ',
			'm.id = d.voucher_master_id ',
		    'WHERE ',
			'm.VoucherDate BETWEEN \'', p_start_date, '\' AND \'', p_end_date, '\' ',
		       'AND m.IsApprove = ', p_IsApprove, ' ',
			'AND m.VoucharTypeId = ', p_VoucharTypeId, ' ',
		    'GROUP BY ',
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed;'
		);
	       
			    END IF;
	    END IF;

	    
	    PREPARE stmt FROM @sql_query;
	    EXECUTE stmt;
	    DEALLOCATE PREPARE stmt;
		
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
        DB::unprepared("DROP PROCEDURE IF EXISTS GetVoucherData");
    }
};
