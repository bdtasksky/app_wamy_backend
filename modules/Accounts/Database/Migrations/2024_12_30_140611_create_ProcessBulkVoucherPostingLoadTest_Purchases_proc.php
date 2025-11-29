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
        DB::unprepared("CREATE PROCEDURE `ProcessBulkVoucherPostingLoadTest_Purchases`()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE n_bill_id INT;
    DECLARE n_voucher_event_code VARCHAR(255);
    DECLARE message VARCHAR(10000); 
    
    
    DECLARE cur CURSOR FOR
   
        SELECT purID, voucher_event_code 
        FROM purchaseitem 
        WHERE voucher_event_code IS NOT NULL;

    

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK'; 
        ROLLBACK;  
        RESIGNAL;  
    END;

    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    
    START TRANSACTION;    

    
    OPEN cur;

    
    read_loop: LOOP
        
        FETCH cur INTO n_bill_id, n_voucher_event_code;
        
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        
        CALL AccIntegrationVoucherPosting(n_bill_id, n_voucher_event_code, @output_message);
       
        UPDATE purchaseitem 
        SET VoucherNumber = NULL 
        WHERE purID = n_bill_id AND voucher_event_code = n_voucher_event_code;
        
        
     
        
        SET message = CONCAT(IFNULL(message, ''), n_bill_id, ',');
    END LOOP;

    
    CLOSE cur;

    
    SELECT message;

    
    COMMIT;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS ProcessBulkVoucherPostingLoadTest_Purchases");
    }
};
