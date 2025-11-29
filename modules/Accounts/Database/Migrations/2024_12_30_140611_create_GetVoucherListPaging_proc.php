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
        DB::unprepared("CREATE DEFINER=`remote`@`%` PROCEDURE `GetVoucherListPaging`(IN `p_VoucharTypeId` INT, IN `p_IsApprove` INT, IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_limit` INT, IN `p_page_number` INT, OUT `op_total_row` INT)
BEGIN
    DECLARE l_previous_fiyear_id INT;
    DECLARE l_current_fiyear_id INT;
    DECLARE l_current_fy_status INT;
    DECLARE p_offset INT;
    

    SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_voucher_table (
         voucher_master_id INT, 
         VoucharTypeId INT,
         VoucherNumber VARCHAR(191),
         VoucherDate DATE,
         Remarks VARCHAR(191),
         TranAmount VARCHAR(191),
         IsApprove INT,
         dr VARCHAR(191),
         cr VARCHAR(191),
         IsYearClosed INT
    );
    
  DELETE FROM temp_voucher_table;  

    
    SELECT fiyear_id, is_active
    INTO l_current_fiyear_id, l_current_fy_status
    FROM acc_financialyear f
    WHERE f.start_date <= p_start_date
    ORDER BY f.start_date DESC
    LIMIT 1;

    
    IF l_current_fy_status = 1 THEN    
        
        IF p_VoucharTypeId = 0 THEN
            IF p_IsApprove = -1 THEN
                
               INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed
                HAVING 
                    dr != cr ORDER BY  m.VoucherDate DESC );
                
            
	    SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	    SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	    SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;
	    
            DROP TEMPORARY TABLE IF EXISTS temp_voucher_table;

            ELSE
                
                INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed
                HAVING 
                    dr != cr ORDER BY  m.VoucherDate DESC);

                
	        SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	        SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	        SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;
	        
                DROP TEMPORARY TABLE IF EXISTS temp_voucher_table;
	        
            END IF;
        ELSEIF p_VoucharTypeId = -1 THEN
            
            IF p_IsApprove = -1 THEN
                
                INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed ORDER BY  m.VoucherDate DESC);

                
	        SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	        SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	        SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;    
	        
                DROP TEMPORARY TABLE IF EXISTS temp_voucher_table;
            ELSE
                
                INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed ORDER BY  m.VoucherDate DESC);
                
		
	        SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	        SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	        SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;  
	        
                DROP TEMPORARY TABLE IF EXISTS temp_voucher_table; 
	        		
            END IF;
        ELSE
            
            IF p_IsApprove = -1 THEN
                
                INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed ORDER BY  m.VoucherDate DESC);

	        
	        SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	        SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	        SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;  
	        
                DROP TEMPORARY TABLE IF EXISTS temp_voucher_table; 
            ELSE
                
                INSERT INTO temp_voucher_table(SELECT 
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
                    m.id, 
		    m.VoucharTypeId, 
		    m.VoucherNumber, 
		    m.VoucherDate, 
		    m.Remarks, 
		    m.TranAmount, 
		    m.IsApprove, 
		    m.IsYearClosed ORDER BY  m.VoucherDate DESC);

               
	        SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	        SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);
	        SELECT * FROM temp_voucher_table LIMIT p_limit OFFSET p_offset;   
	        
                DROP TEMPORARY TABLE IF EXISTS temp_voucher_table;
            END IF;
        END IF;
      ELSE
        
        
	    IF p_VoucharTypeId= -1 THEN
	    
	    
			     
			    IF p_IsApprove= -1 THEN
			    
			    SET @sql_query = CONCAT(
		    'INSERT INTO temp_voucher_table(SELECT ',
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
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed ORDER BY  m.VoucherDate DESC);'
		);
	       
			    ELSE
			    
			    SET @sql_query = CONCAT(
		    'INSERT INTO temp_voucher_table(SELECT ',
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
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed ORDER BY  m.VoucherDate DESC);'
		);
	       
			    END IF;
	    ELSE 
	    
			     
			    IF p_IsApprove= -1 THEN
			    
			   SET @sql_query = CONCAT(
		    'INSERT INTO temp_voucher_table(SELECT ',
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
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed ORDER BY  m.VoucherDate DESC);'
		);
	       
			    ELSE
			    
			   SET @sql_query = CONCAT(
		    'INSERT INTO temp_voucher_table(SELECT ',
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
			'm.id, m.VoucharTypeId, m.VoucherNumber, m.VoucherDate, m.Remarks, m.TranAmount, m.IsApprove, m.IsYearClosed ORDER BY  m.VoucherDate DESC);'
		);
	       
			    END IF;
	    END IF;

	    
	    PREPARE stmt FROM @sql_query;
	    EXECUTE stmt;
	    DEALLOCATE PREPARE stmt;
	    
	    
	    SELECT COUNT(*) INTO op_total_row FROM temp_voucher_table;
	    SET p_offset= IFNULL(((p_page_number-1) * p_limit),0);

	    
	    
	    IF p_limit = -1 THEN
            SELECT * FROM temp_voucher_table ORDER BY VoucherDate DESC; 
            ELSE
            SELECT * FROM temp_voucher_table ORDER BY VoucherDate DESC LIMIT p_limit OFFSET p_offset;   
            END IF;

	    
	    
	    
	    DROP TEMPORARY TABLE IF EXISTS temp_voucher_table; 
	    
	    
		
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
        DB::unprepared("DROP PROCEDURE IF EXISTS GetVoucherListPaging");
    }
};
