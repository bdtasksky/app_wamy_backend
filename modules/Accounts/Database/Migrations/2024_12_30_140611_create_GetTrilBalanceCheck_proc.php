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
        DB::unprepared("CREATE PROCEDURE `GetTrilBalanceCheck`(IN `p_branch_Id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE, OUT `is_equalsum` BOOL)
BEGIN
    
    DECLARE l_acc_id INT;
    DECLARE l_acc_name VARCHAR(191);
    DECLARE l_account_code VARCHAR(191);
    DECLARE n_acc_name VARCHAR(191);
    DECLARE t_debit DOUBLE(15,2);
    DECLARE t_credit DOUBLE(15,2);
    
    DECLARE l_is_subtype INT;
    DECLARE l_subtype_id INT;
    
    DECLARE get_acc_type VARCHAR(2);
    DECLARE done INT DEFAULT 0;

    DECLARE l_id INT;
    DECLARE l_name VARCHAR(191);

    
    DECLARE cur CURSOR FOR
        SELECT a.account_name, a.account_code, a.id, t.account_type_name, a.is_subtype, a.subtype_id
        FROM acc_coas a
        INNER JOIN acc_types AS t ON t.id = a.acc_type_id
        WHERE a.is_active = 1 AND a.head_level = 4 AND a.deleted_at IS NULL
        ORDER BY a.id;

    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    

    DROP TEMPORARY TABLE IF EXISTS temp_table_ye;
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_table_ye (
        nacc_name VARCHAR(191),
        acc_id INT(11),
        account_code VARCHAR(191),
        acc_name VARCHAR(191),
        debit DOUBLE(15,2),
        credit DOUBLE(15,2),
        subtype_id INT,
        subcode_id INT
    );
    DELETE FROM temp_table_ye;

    
    OPEN cur;

    main_loop: LOOP
        FETCH cur INTO l_acc_name, l_account_code, l_acc_id, n_acc_name, l_is_subtype, l_subtype_id;
        
        IF done THEN
            LEAVE main_loop;
        END IF;

        
        SELECT GetAccType(l_acc_id) INTO get_acc_type;

        
        CALL GetLedger_OCB(p_branch_Id, l_acc_id, p_from_date, p_to_date, 
            @p_opening_balance, @p_closing_balance, @p_transaction_balance);

        
        IF l_is_subtype = 1 AND l_subtype_id > 1 THEN
            
            BEGIN
                DECLARE done_subcode INT DEFAULT 0;
                
                DECLARE cur_subcode CURSOR FOR
                    SELECT id, NAME FROM acc_subcode WHERE subTypeID = l_subtype_id;
                
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_subcode = 1;

                
                OPEN cur_subcode;
                subcode_loop: LOOP
                    FETCH cur_subcode INTO l_id, l_name;
                    
                    IF done_subcode THEN
                        SET done_subcode = 0; 
                        LEAVE subcode_loop;
                    END IF;

                    
                    CALL GetLedgerSubCode_OCB(p_branch_Id,l_acc_id, l_id, p_from_date, p_to_date,@p_opening_balance, @p_closing_balance, @p_transaction_balance);
                    
                    IF get_acc_type = 'DR' THEN
                        SET t_debit = @p_closing_balance;
                        SET t_credit = 0.00;
                    ELSEIF get_acc_type = 'CR' THEN
                        SET t_debit = 0.00;
                        SET t_credit = @p_closing_balance;
                    END IF;

                    
                    IF @p_closing_balance <> 0.00 THEN
                        INSERT INTO temp_table_ye (nacc_name, account_code, acc_id, acc_name, debit, credit, subtype_id, subcode_id)
                        VALUES (n_acc_name, l_account_code, l_acc_id, l_acc_name, t_debit, t_credit, l_subtype_id, l_id);
                    END IF;
                    
                END LOOP;

                
                CLOSE cur_subcode;
            END;
        ELSE
            
            IF get_acc_type = 'DR' THEN
                SET t_debit = @p_closing_balance;
                SET t_credit = 0.00;
            ELSEIF get_acc_type = 'CR' THEN
                SET t_debit = 0.00;
                SET t_credit = @p_closing_balance;
            END IF;

            
            INSERT INTO temp_table_ye (nacc_name, account_code, acc_id, acc_name, debit, credit, subtype_id, subcode_id)
            VALUES (n_acc_name, l_account_code, l_acc_id, l_acc_name, t_debit, t_credit, 1, 1);
        END IF;

    END LOOP;

    
    CLOSE cur;

    
    SET @sum_of_debit = (SELECT IFNULL(SUM(debit), 0) FROM temp_table_ye);
    SET @sum_of_credit = (SELECT IFNULL(SUM(credit), 0) FROM temp_table_ye);

    
    IF @sum_of_debit = @sum_of_credit THEN
        SET is_equalsum = TRUE;
    ELSE
        SET is_equalsum = FALSE;
    END IF;


SELECT * FROM temp_table_ye;
    
    

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetTrilBalanceCheck");
    }
};
