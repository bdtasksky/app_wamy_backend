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
        DB::unprepared("CREATE PROCEDURE `AccVoucherApproveBulk`(IN `jsonData` JSON, OUT `message` VARCHAR(255))
BEGIN
    
    DECLARE voucher_id INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE max_elements INT;
    
    DECLARE voucher_number VARCHAR(255);
    DECLARE voucher_date DATE;
    DECLARE company_id INT DEFAULT 0;
    DECLARE branch_id INT DEFAULT 0;
    DECLARE voucher_type_id INT DEFAULT 0;
    DECLARE voucher_event_code VARCHAR(25);
    DECLARE voucher_remarks VARCHAR(255);
    DECLARE created_by VARCHAR(100);
    DECLARE financialYearId INT;
    DECLARE created_date DATE;
    DECLARE purchaseID  INT;
    DECLARE billID  INT;
    DECLARE serviceID  INT;    
    
    
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;
    SET max_elements = JSON_LENGTH(jsonData);
    
    WHILE i < max_elements DO
        SET voucher_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherId')));

        IF voucher_id IS NOT NULL THEN
           
           
           
           
           SELECT voucher_id;

           CALL AccVoucherApprove(voucher_id, @app_message); 

 
        END IF;

        
        SET i = i + 1;
    END WHILE;

    
    SET message = CONCAT(i ,' Voucher Approved Successfully');

    
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccVoucherApproveBulk");
    }
};
