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
        DB::unprepared("CREATE PROCEDURE `GetNextYearOpeningBalanceView`(IN `p_branch_Id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE)
BEGIN
    DECLARE l_nature_account_name VARCHAR(191);
    DECLARE l_acc_id INT;
    DECLARE l_acc_name VARCHAR(191);
    DECLARE n_acc_name VARCHAR(191);
    DECLARE t_debit DOUBLE(15,2);
    DECLARE t_credit DOUBLE(15,2);
    DECLARE get_acc_type VARCHAR(2);
    DECLARE done INT DEFAULT 0;
    DECLARE coa_current_year_profitloss INT;
    
    DECLARE total_debit DOUBLE(15,2);
    DECLARE total_credit DOUBLE(15,2);
    
    
    
    DECLARE cur CURSOR FOR

         SELECT  
        (SELECT account_name FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id)))) AS nature_account_name,
        al.id AS ledger_id, 
        al.account_name AS acc_name
        FROM acc_coas al
        WHERE al.head_level=4 AND al.is_active = 1 AND acc_type_id NOT IN(2,3) AND al.deleted_at IS NULL ORDER BY  nature_account_name ;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

     DROP TABLE IF EXISTS temp_table_bs;
     
        CREATE TABLE temp_table_bs (
        nature_account_name VARCHAR(191),
        ledger_id INT,
        acc_name VARCHAR(191),
        debit DOUBLE(15,2),
        credit DOUBLE(15,2)
        );

 
        SELECT acc_coa_id INTO coa_current_year_profitloss 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 14 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO l_nature_account_name, l_acc_id, l_acc_name;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        SELECT GetAccType(l_acc_id) INTO get_acc_type;

        CALL GetLedger_OCB(p_branch_Id,l_acc_id, p_from_date, p_to_date, @p_opening_balance, @p_closing_balance, @p_trangection_balance);

        
            IF get_acc_type = 'DR' THEN
                SET t_debit = @p_closing_balance;
                SET t_credit = 0.00;
            ELSEIF get_acc_type = 'CR' THEN
                SET t_debit = 0.00;
                SET t_credit = @p_closing_balance;
            END IF;



		IF (l_acc_id = coa_current_year_profitloss) THEN
		    
		    CALL GetProfitLoassAmount(p_branch_Id, p_from_date, p_to_date, @profit_loss);
		    INSERT INTO temp_table_bs (nature_account_name, ledger_id, acc_name, debit, credit)
		    VALUES (l_nature_account_name, l_acc_id, l_acc_name, 0.00, @profit_loss);
		    
		ELSE
		    
		    INSERT INTO temp_table_bs (nature_account_name, ledger_id, acc_name, debit, credit)
		    VALUES (l_nature_account_name, l_acc_id, l_acc_name, t_debit, t_credit);
		    
		END IF;
    END LOOP;
    CLOSE cur;


    SELECT SUM(debit), SUM(credit) INTO total_debit, total_credit FROM temp_table_bs;
    
    INSERT INTO temp_table_bs (nature_account_name, ledger_id, acc_name, debit, credit)
    VALUES ('Total', NULL, 'Total', total_debit, total_credit);

    
    SELECT * FROM temp_table_bs;

    
    DROP TABLE IF EXISTS temp_table_bs; 
    
    
    
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetNextYearOpeningBalanceView");
    }
};
