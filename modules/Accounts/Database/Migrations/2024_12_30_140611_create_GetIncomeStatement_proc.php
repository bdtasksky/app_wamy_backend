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
        DB::unprepared("CREATE DEFINER=`remote`@`%` PROCEDURE `GetIncomeStatement`(
    IN `p_branch_Id` INT,
    IN `p_from_date` DATE,
    IN `p_to_date` DATE,
    in  p_closing_inventory decimal
)
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
    
    DECLARE r_report_id INT;
    DECLARE r_srl INT;
    DECLARE r_r_sign VARCHAR(191);
    DECLARE r_r_space VARCHAR(191);
    DECLARE r_coa_acc_id VARCHAR(191);
    DECLARE r_sub_coa_acc_id VARCHAR(191);
    DECLARE r_account_name VARCHAR(191);
    DECLARE r_value_colum VARCHAR(191);
    DECLARE r_is_active VARCHAR(191);
    
    DECLARE v_srl INT;
    DECLARE v_description VARCHAR(191);
    DECLARE v_amountA DOUBLE(15,2);
    DECLARE v_amountB DOUBLE(15,2);
    DECLARE v_amountC DOUBLE(15,2);
    
    DECLARE done INT DEFAULT 0;

    DECLARE cur CURSOR FOR
        SELECT report_id, srl, r_sign, r_space, coa_acc_id, sub_coa_acc_id, account_name, value_colum, is_active
        FROM acc_report_formate
        WHERE is_active = 1 AND report_id = 1;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Temporary table creation
    DROP TEMPORARY TABLE IF EXISTS temp_table_IncomeStatement;
    CREATE TEMPORARY TABLE temp_table_IncomeStatement (
        id INT AUTO_INCREMENT PRIMARY KEY,
        srl INT,
        description VARCHAR(191),
        amountA DOUBLE(15,2),
        amountB DOUBLE(15,2),
        amountC DOUBLE(15,2)
    );

    -- Open cursor
    OPEN cur;

    -- Cursor loop
    read_loop: LOOP
        FETCH cur INTO r_report_id, r_srl, r_r_sign, r_r_space, r_coa_acc_id, r_sub_coa_acc_id, r_account_name, r_value_colum, r_is_active;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Initialize variables
        SET v_srl = r_srl;
        SET v_description = r_account_name;
        SET v_amountA = 0.00;
        SET v_amountB = 0.00;
        SET v_amountC = 0.00;
        
        -- Logic handling based on r_sub_coa_acc_id
        IF r_sub_coa_acc_id IS NULL THEN
            -- Sub-COA is NULL: Process account-specific data
            IF r_coa_acc_id IS NOT NULL THEN
   
                -- Fetch account type and calculate balances
                SELECT GetAccType(r_coa_acc_id) INTO get_acc_type;
                CALL GetLedger_OCB(p_branch_Id, r_coa_acc_id, p_from_date, p_to_date, @p_opening_balance, @p_closing_balance, @p_trangection_balance);
                SELECT account_name INTO r_account_name FROM `acc_coas` al WHERE al.id= r_coa_acc_id  LIMIT 1;
                IF get_acc_type = 'DR' THEN
                    SET t_debit = IFNULL( ABS( @p_closing_balance),0.00);
                    SET t_credit = 0.00;
                ELSEIF get_acc_type = 'CR' THEN
                    SET t_debit = 0.00;
                    SET t_credit = IFNULL( ABS( @p_closing_balance),0.00);
                END IF;
                
			IF r_value_colum = 'A' THEN
			    SET v_amountA = IFNULL(ABS( @p_closing_balance),0.00);
			ELSEIF r_value_colum = 'B' THEN
			    SET v_amountB = IFNULL( ABS( @p_closing_balance),0.00);
			ELSEIF r_value_colum = 'C' THEN
			    SET v_amountC = IFNULL( ABS( @p_closing_balance),0.00);
			ELSE
			    SET v_amountA = 0.00;
			    SET v_amountB = 0.00;
			    SET v_amountC = 0.00;
			END IF;

			SET v_description='';
                        SET v_description = CONCAT(r_r_space , r_r_sign , r_account_name);

                -- Insert account-specific record
                INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
                VALUES (v_srl, v_description, v_amountA, v_amountB, v_amountC);
            ELSE

			IF r_r_sign = '=' THEN
			       
			               -- Pre-fetch required values for the current srl into variables
				SELECT 
				    p_colum_amount_1,
				    p_colum_amount_2,
				    p_srl_row_val_1,
				    p_srl_row_val_2
				INTO 
				    @p_colum_amount_1,
				    @p_colum_amount_2,
				    @p_srl_row_val_1,
				    @p_srl_row_val_2
				FROM acc_report_formate
				WHERE srl = v_srl;

				-- Row 1 Calculation
				SELECT 
				    CASE 
					WHEN @p_colum_amount_1 = 'A' THEN (amountA)
					WHEN @p_colum_amount_1 = 'B' THEN (amountB)
					WHEN @p_colum_amount_1 = 'C' THEN (amountC)
					ELSE 0
				    END AS p_row_1_amount
				INTO @p_row_1_amount
				FROM temp_table_IncomeStatement
				WHERE srl = @p_srl_row_val_1
				ORDER BY id DESC
				LIMIT 1;

				-- Row 2 Calculation
				SELECT 
				    CASE 
					WHEN @p_colum_amount_2 = 'A' THEN (amountA)
					WHEN @p_colum_amount_2 = 'B' THEN (amountB)
					WHEN @p_colum_amount_2 = 'C' THEN (amountC)
					ELSE 0
				    END AS p_row_2_amount
				INTO @p_row_2_amount
				FROM temp_table_IncomeStatement
				WHERE srl = @p_srl_row_val_2
			        ORDER BY id DESC
				LIMIT 1;


				-- Calculate Closing Balance
				SET @p_closing_balance = 
				    CASE 
					WHEN (SELECT p_sign FROM acc_report_formate WHERE srl = v_srl) = '+' THEN @p_row_1_amount + @p_row_2_amount
					WHEN (SELECT p_sign FROM acc_report_formate WHERE srl = v_srl) = '-' THEN @p_row_1_amount - @p_row_2_amount
					ELSE 0
				    END;

				-- Assign Closing Balance to Variables
				CASE 
				    WHEN r_value_colum = 'A' THEN SET v_amountA = (@p_closing_balance);
				    WHEN r_value_colum = 'B' THEN SET v_amountB = (@p_closing_balance);
				    WHEN r_value_colum = 'C' THEN SET v_amountC = (@p_closing_balance);
				    ELSE 
					SET v_amountA = 0.00;
					SET v_amountB = 0.00;
					SET v_amountC = 0.00;
				END CASE;
                                SET v_description='';
                                SET v_description = CONCAT(r_r_space , r_r_sign , r_account_name);
                                

                                
                                
				-- Insert into Summary Table
				INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
				VALUES (v_srl, v_description, v_amountA, v_amountB, v_amountC);
			ELSE
			
				IF v_srl=16 THEN
				 -- Closing Inventory value sat 
				 CASE 
				    WHEN r_value_colum = 'A' THEN SET v_amountA = IFNULL( p_closing_inventory,0.00);
				    WHEN r_value_colum = 'B' THEN SET v_amountB = IFNULL( p_closing_inventory,0.00);
				    WHEN r_value_colum = 'C' THEN SET v_amountC = IFNULL( p_closing_inventory,0.00);
				    ELSE 
					SET v_amountA = 0.00;
					SET v_amountB = 0.00;
					SET v_amountC = 0.00;
				END CASE;
				SET v_description='';
				SET v_description = CONCAT(r_r_space , r_r_sign , r_account_name);
						-- Insert into Summary Table
				INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
				VALUES (v_srl, v_description, v_amountA, v_amountB, v_amountC);
				
				else
				        SET v_amountA = 0.00;
					SET v_amountB = 0.00;
					SET v_amountC = 0.00;
					SET v_description='';
					SET v_description = CONCAT(r_r_space , r_r_sign , r_account_name);
					-- Insert into Summary Table
					INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
					VALUES (v_srl, v_description, v_amountA, v_amountB, v_amountC);
				end if;
			
			       
			END IF;
			
			

            END IF;
        ELSE
            -- Group-level processing for non-NULL r_sub_coa_acc_id

            CALL GetGroupLedger(p_branch_Id, r_sub_coa_acc_id , p_from_date, p_to_date);
            
            INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
            SELECT v_srl, CONCAT(r_r_space , acc_name) ,IFNULL( ABS(debit-credit),0.00) AS amount,0.00,0.00 FROM temp_table_gl;
            
            SELECT ABS(SUM(debit)-SUM(credit))INTO @p_closing_balance FROM temp_table_gl;
            DROP TABLE IF EXISTS temp_table_gl;
		 CASE 
		    WHEN r_value_colum = 'A' THEN SET v_amountA = IFNULL( @p_closing_balance,0.00);
		    WHEN r_value_colum = 'B' THEN SET v_amountB = IFNULL( @p_closing_balance,0.00);
		    WHEN r_value_colum = 'C' THEN SET v_amountC = IFNULL( @p_closing_balance,0.00);
		    ELSE 
			SET v_amountA = 0.00;
			SET v_amountB = 0.00;
			SET v_amountC = 0.00;
		END CASE;
		SET v_description='';
		SET v_description = CONCAT(r_r_space , r_r_sign , r_account_name);
				-- Insert into Summary Table
		INSERT INTO temp_table_IncomeStatement (srl, description, amountA, amountB, amountC)
		VALUES (v_srl, v_description, v_amountA, v_amountB, v_amountC);
           
        END IF;

    END LOOP;

    -- Close cursor
    CLOSE cur;

     SELECT * FROM  temp_table_IncomeStatement;
    -- SELECT * FROM acc_report_formate;
    -- Drop temporary table at the end
    DROP TEMPORARY TABLE IF EXISTS temp_table_IncomeStatement;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetIncomeStatement");
    }
};
