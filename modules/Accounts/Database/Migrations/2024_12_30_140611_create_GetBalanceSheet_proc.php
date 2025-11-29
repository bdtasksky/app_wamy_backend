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
        DB::unprepared("CREATE PROCEDURE `GetBalanceSheet`(IN `p_branch_Id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE)
BEGIN
    DECLARE n_nature_id INT;
    DECLARE g_group_id INT;
    DECLARE sg_sub_group_id INT;
    DECLARE l_acc_id INT;
    DECLARE l_acc_name VARCHAR(191);
    DECLARE n_acc_name VARCHAR(191);
    DECLARE t_debit DOUBLE(15,2);
    DECLARE t_credit DOUBLE(15,2);
    DECLARE get_acc_type VARCHAR(2);
    DECLARE done INT DEFAULT 0;
    DECLARE coa_current_year_profitloss INT;
    DECLARE cur CURSOR FOR
   
        SELECT  
        (SELECT id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id)))) AS nature_id,
        (SELECT id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id))) AS group_id,
        (SELECT id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id)) AS sub_group_id,
        al.id AS ledger_id, 
        al.account_name AS acc_name
        FROM acc_coas al
        WHERE al.head_level=4 AND al.is_active = 1 AND al.deleted_at IS NULL ORDER BY  nature_id ;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

     DROP TABLE IF EXISTS temp_table_bs;
     
        CREATE TABLE temp_table_bs (
        nature_id INT,
	      group_id INT,
	      sub_group_id INT,
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
        FETCH cur INTO n_nature_id,g_group_id,sg_sub_group_id, l_acc_id, l_acc_name;
        
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
		    INSERT INTO temp_table_bs (nature_id, group_id, sub_group_id, ledger_id, acc_name, debit, credit)
		    VALUES (n_nature_id, g_group_id, sg_sub_group_id, l_acc_id, l_acc_name, 0.00, @profit_loss);
		    
		ELSE
		    
		    INSERT INTO temp_table_bs (nature_id, group_id, sub_group_id, ledger_id, acc_name, debit, credit)
		    VALUES (n_nature_id, g_group_id, sg_sub_group_id, l_acc_id, l_acc_name, t_debit, t_credit);
		    
		END IF;


    END LOOP;
    CLOSE cur;


         SELECT 
        t.nature_id,
        n.account_name AS nature_name,
        (SELECT COALESCE(SUM(debit), 0) FROM temp_table_bs WHERE nature_id = t.nature_id) AS nature_amount_debit,
        (SELECT COALESCE(SUM(credit), 0) FROM temp_table_bs WHERE nature_id = t.nature_id) AS nature_amount_credit,
         
	      t.group_id,
	      g.account_name AS group_name,
        (SELECT COALESCE(SUM(debit), 0) FROM temp_table_bs WHERE group_id = t.group_id) AS group_amount_debit,
        (SELECT COALESCE(SUM(credit), 0) FROM temp_table_bs WHERE group_id = t.group_id) AS group_amount_credit,
	      
	      t.sub_group_id,
        sg.account_name AS sub_group_name,
        (SELECT COALESCE(SUM(debit), 0) FROM temp_table_bs WHERE sub_group_id = t.sub_group_id) AS sub_group_amount_debit,
        (SELECT COALESCE(SUM(credit), 0) FROM temp_table_bs WHERE sub_group_id = t.sub_group_id) AS sub_group_amount_credit,
	      
        t.ledger_id,
        t.acc_name AS ledger_name,
        t.debit,
        t.credit
        FROM temp_table_bs t
        INNER JOIN  acc_coas sg ON sg.id= t.sub_group_id
        INNER JOIN  acc_coas g ON g.id= t.group_id
        INNER JOIN  acc_coas n ON n.id= t.nature_id;
        
        
    
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
        DB::unprepared("DROP PROCEDURE IF EXISTS GetBalanceSheet");
    }
};
