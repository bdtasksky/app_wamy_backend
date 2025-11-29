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
        DB::unprepared("CREATE PROCEDURE `GetProfitLoassAmount`(IN `p_branch_Id` INT, IN `p_from_date` DATE, IN `p_to_date` DATE, OUT `profit_loss` DOUBLE)
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

    DECLARE cur CURSOR FOR
    
    
        
         SELECT  
        (SELECT id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id)))) AS nature_id,
        (SELECT id FROM acc_coas WHERE id=(SELECT parent_id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id))) AS group_id,
        (SELECT id FROM acc_coas WHERE id=( SELECT parent_id FROM acc_coas WHERE id=al.id)) AS sub_group_id,
        al.id AS ledger_id, 
        al.account_name AS acc_name
        FROM acc_coas al
        WHERE al.head_level=4 AND al.is_active = 1 AND al.acc_type_id IN (3,2) AND al.deleted_at IS NULL  ORDER BY  nature_id DESC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

     DROP TABLE IF EXISTS temp_table_pl;
     
        CREATE TABLE temp_table_pl (
        nature_id INT,
	group_id INT,
	sub_group_id INT,
        ledger_id INT,
        acc_name VARCHAR(191),
        debit DOUBLE(15,2),
        credit DOUBLE(15,2)
        );

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
            INSERT INTO temp_table_pl (nature_id,group_id,sub_group_id,ledger_id, acc_name, debit, credit)
            VALUES (n_nature_id,g_group_id,sg_sub_group_id,l_acc_id, l_acc_name, t_debit, t_credit);
     

    END LOOP;
    CLOSE cur;

    
    SELECT SUM(credit) - SUM(debit) INTO profit_loss FROM temp_table_pl;

    
    DROP TABLE IF EXISTS temp_table_pl;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetProfitLoassAmount");
    }
};
