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
        DB::unprepared("CREATE PROCEDURE `GetTrilBalance`(IN `p_branch_Id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE)
BEGIN
    
    DECLARE l_acc_id INT;
    DECLARE l_acc_name VARCHAR(191);
    DECLARE n_acc_name VARCHAR(191);
    DECLARE t_debit DOUBLE(15,2);
    DECLARE t_credit DOUBLE(15,2);
    DECLARE get_acc_type VARCHAR(2);
    DECLARE done INT DEFAULT 0;

    
    DECLARE cur CURSOR FOR
       
    SELECT t.account_type_name, a.id, a.account_name
    FROM acc_coas a
    INNER JOIN acc_types AS t ON t.id = a.acc_type_id
    WHERE a.is_active = 1 AND a.head_level=4 AND a.deleted_at IS NULL ORDER BY a.id;


    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_table (
	nacc_name VARCHAR(191),
	acc_id INT(11),
        acc_name VARCHAR(191),
        debit DOUBLE(15,2),
        credit DOUBLE(15,2)
    );

    
    
    
    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO n_acc_name, l_acc_id, l_acc_name;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        
        SELECT GetAccType(l_acc_id) INTO get_acc_type;



CALL GetLedger_OCB(p_branch_Id,l_acc_id,p_from_date, p_to_date, @p_opening_balance, @p_closing_balance, @p_trangection_balance);




        
        IF get_acc_type = 'DR' THEN
            SET t_debit =  @p_closing_balance;
            SET t_credit =0.00;
        ELSEIF get_acc_type = 'CR' THEN
            SET t_debit = 0.00;
            SET t_credit = @p_closing_balance;
        END IF;

        
        INSERT INTO temp_table (nacc_name,acc_id,acc_name, debit, credit)
        VALUES (n_acc_name,l_acc_id,l_acc_name, t_debit, t_credit);
        


    END LOOP;

    
    CLOSE cur;




   SET @sum_of_debit := (SELECT SUM(debit) FROM temp_table);
   SET @sum_of_credit := (SELECT SUM(credit) FROM temp_table);
   
			INSERT INTO temp_table (acc_name, debit, credit)
        	VALUES ('Total', @sum_of_debit, @sum_of_credit);

    
    SELECT * FROM temp_table;

    
    DROP TEMPORARY TABLE IF EXISTS temp_table;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetTrilBalance");
    }
};
