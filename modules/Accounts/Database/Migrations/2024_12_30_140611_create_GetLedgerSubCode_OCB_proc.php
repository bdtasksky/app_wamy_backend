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
        DB::unprepared("CREATE PROCEDURE `GetLedgerSubCode_OCB`(IN `p_branch_Id` INT, IN `p_acc_coa_id` INT, IN `subCode_id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE, OUT `p_opening_balance` DOUBLE(15,2), OUT `p_closing_balance` DOUBLE(15,2), OUT `p_trangection_balance` DOUBLE(15,2))
BEGIN
    DECLARE opening_date DATE;
    DECLARE opening_debit DOUBLE(15,2);
    DECLARE opening_credit DOUBLE(15,2);
    DECLARE opening_balance DOUBLE(15,2);
    DECLARE opening_financial_year_id INT;
    DECLARE get_acc_type VARCHAR(2);
    DECLARE done INT DEFAULT 0;

    DECLARE vh_id INT;
    DECLARE vh_v_no VARCHAR(255);
    DECLARE vh_acc_voucher_type_id INT;
    DECLARE l_rev_acc_name VARCHAR(191);
    DECLARE vh_remarks TEXT;
    DECLARE t_v_date DATE;
    DECLARE t_debit DOUBLE(15,2);
    DECLARE t_credit DOUBLE(15,2);
    DECLARE vh_refno VARCHAR(100);
    
    DECLARE l_previous_fiyear_id INT;
    DECLARE l_current_fiyear_id INT;
    DECLARE l_current_fy_status INT;

    
  
        
    DECLARE cur_cy CURSOR FOR  SELECT 
         t.voucher_master_id, t.VoucherNumber, a.account_name, t.Remarks, t.VoucherDate,
	IFNULL(t.`Dr_Amount`, 0.00), IFNULL(t.`Cr_Amount`, 0.00)
	 FROM acc_transactions t
	INNER JOIN acc_coas a ON a.id=t.reverse_acc_coa_id WHERE  
	t.subcode_id= subCode_id AND   
	t.acc_coa_id=p_acc_coa_id AND 
	t.VoucherDate BETWEEN (SELECT start_date FROM acc_financialyear f WHERE f.is_active= 1 AND f.start_date <=  p_from_date ORDER BY f.start_date DESC LIMIT 1) AND p_to_date AND 
	t.BranchId=p_branch_Id
	ORDER BY t.VoucherDate ASC, t.id ASC;


  DECLARE cur_py CURSOR FOR SELECT voucher_master_id, VoucherNumber, account_name, Remarks, VoucherDate, Dr_Amount, Cr_Amount  FROM temp_cursor_results;
        
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_cursor_results (
        voucher_master_id INT,
        VoucherNumber VARCHAR(255),
        account_name VARCHAR(191),
        Remarks TEXT,
        VoucherDate DATE,
        Dr_Amount DOUBLE(15,2),
        Cr_Amount DOUBLE(15,2)
    );

    
    SELECT fiyear_id 
    INTO l_previous_fiyear_id
    FROM acc_financialyear f 
    WHERE end_date = (
        SELECT DATE_SUB(start_date, INTERVAL 1 DAY) 
        FROM acc_financialyear f2 
        WHERE f2.start_date <= p_from_date
        ORDER BY f2.start_date DESC 
        LIMIT 1
    );
    
    SELECT fiyear_id, is_active
    INTO l_current_fiyear_id, l_current_fy_status
    FROM acc_financialyear f
    WHERE f.start_date <= p_from_date
    ORDER BY f.start_date DESC
    LIMIT 1;
	   
	   IF l_current_fy_status=2 THEN
	    
	    SET @sql_query = CONCAT(
		'INSERT INTO temp_cursor_results (voucher_master_id, VoucherNumber, account_name, Remarks, VoucherDate, Dr_Amount, Cr_Amount, VoucharTypeId, refno) ',
		'SELECT t.voucher_master_id, t.VoucherNumber, a.account_name, t.Remarks, t.VoucherDate, ',
		'IFNULL(t.Dr_Amount, 0.00), IFNULL(t.Cr_Amount, 0.00), t.VoucharTypeId, t.billid AS refno ',
		'FROM acc_transactions', l_current_fiyear_id, ' t ',
		'INNER JOIN acc_coas a ON a.id = t.reverse_acc_coa_id ',
		'WHERE t.subcode_id=', subCode_id,' AND t.BranchId = ', p_branch_Id, ' AND t.acc_coa_id = ', p_acc_coa_id, ' ',
		'AND t.VoucherDate BETWEEN (SELECT start_date FROM acc_financialyear f WHERE f.is_active = 2 AND f.start_date <= \"', p_from_date, '\" ORDER BY f.start_date DESC LIMIT 1) ',
		'AND \"', p_to_date, '\" ORDER BY t.VoucherDate ASC, t.id ASC'
	    );

	    
	    PREPARE stmt FROM @sql_query;
	    EXECUTE stmt;
	    DEALLOCATE PREPARE stmt;
	END IF;
    
    IF EXISTS (SELECT * FROM acc_openingbalance o INNER JOIN acc_coas c ON c.id = o.acc_coa_id WHERE c.id=p_acc_coa_id AND o.financial_year_id = l_previous_fiyear_id AND o.acc_subcode_id = subCode_id AND c.acc_type_id   NOT IN(2,3)) THEN
        SELECT 
            
            CASE 
            WHEN open_date IS NULL OR open_date = p_from_date THEN p_from_date
            ELSE open_date 
            END AS adjusted_open_date,
            SUM(IFNULL(debit, 0.00)) AS total_debit,
            SUM(IFNULL(credit, 0.00)) AS total_credit,
            financial_year_id
        INTO 
            opening_date, 
            opening_debit, 
            opening_credit, 
            opening_financial_year_id
        FROM acc_openingbalance
        WHERE acc_coa_id = p_acc_coa_id AND financial_year_id = l_previous_fiyear_id  AND acc_subcode_id = subCode_id GROUP BY financial_year_id, adjusted_open_date;
    ELSE
        SET opening_date = p_from_date;
        SET opening_debit = 0.00;
        SET opening_credit = 0.00;
        SET opening_financial_year_id = 0;
    END IF;

    SELECT GetAccType(p_acc_coa_id) INTO get_acc_type;

    
    IF get_acc_type = 'DR' THEN
        SET opening_balance = IFNULL((opening_debit-opening_credit), 0.00);
    ELSEIF get_acc_type = 'CR' THEN
        SET opening_balance = IFNULL((opening_credit-opening_debit), 0.00);
    END IF;

   SET p_opening_balance = opening_balance;

IF l_current_fy_status=1 THEN

    OPEN cur_cy;

    read_loop: LOOP
        FETCH cur_cy INTO vh_id, vh_v_no, l_rev_acc_name, vh_remarks, t_v_date, t_debit, t_credit;
        
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        
      IF get_acc_type = 'DR' THEN
            IF t_debit = 0.00 THEN
                SET opening_balance = opening_balance - t_credit;
            ELSE
                SET opening_balance = opening_balance + t_debit;
            END IF;
        ELSEIF get_acc_type = 'CR' THEN
            IF t_credit = 0.00 THEN
                SET opening_balance = opening_balance - t_debit;
            ELSE
                SET opening_balance = opening_balance + t_credit;
            END IF;
        END IF;
    END LOOP;

    
    CLOSE cur_cy;

    
    SET p_closing_balance = opening_balance;
    SET p_trangection_balance = p_closing_balance - p_opening_balance;

END IF;

IF l_current_fy_status=2 THEN

    OPEN cur_py;

    read_loop: LOOP
        FETCH cur_py INTO vh_id, vh_v_no, l_rev_acc_name, vh_remarks, t_v_date, t_debit, t_credit;
        
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        
      IF get_acc_type = 'DR' THEN
            IF t_debit = 0.00 THEN
                SET opening_balance = opening_balance - t_credit;
            ELSE
                SET opening_balance = opening_balance + t_debit;
            END IF;
        ELSEIF get_acc_type = 'CR' THEN
            IF t_credit = 0.00 THEN
                SET opening_balance = opening_balance - t_debit;
            ELSE
                SET opening_balance = opening_balance + t_credit;
            END IF;
        END IF;
    END LOOP;

    
    CLOSE cur_py;
    SET p_closing_balance = opening_balance;
    SET p_trangection_balance = p_closing_balance - p_opening_balance;

END IF;

    
    DROP TEMPORARY TABLE IF EXISTS temp_cursor_results;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetLedgerSubCode_OCB");
    }
};
