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
        DB::unprepared("CREATE DEFINER=`remote`@`%` PROCEDURE `AccVoucherDeleteBulk`(IN `jsonData` JSON, IN `modulename` VARCHAR(25), OUT `message` VARCHAR(255))
BEGIN
    DECLARE in_id INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE max_elements INT;
    DECLARE p_voucher_event_code VARCHAR(25);
    DECLARE app_message VARCHAR(255);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    SET max_elements = JSON_LENGTH(jsonData);

    WHILE i < max_elements DO
        SET in_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].InId')));

        IF in_id IS NOT NULL THEN
            IF modulename = 'POS-SALES' THEN
                SELECT voucher_event_code 
                INTO p_voucher_event_code 
                FROM bill 
                WHERE order_id = in_id 
                LIMIT 1;

                CALL AccVoucherDelete(in_id, p_voucher_event_code, app_message);

            ELSEIF modulename = 'POS-PURCHASES' THEN
                SELECT voucher_event_code 
                INTO p_voucher_event_code 
                FROM purchaseitem 
                WHERE purID = in_id 
                LIMIT 1;

                CALL AccVoucherDelete(in_id, p_voucher_event_code, app_message);
            END IF;
        END IF;

        SET i = i + 1;
    END WHILE;

    SET message = CONCAT(i, ' ', modulename, ' Record(s) Deleted Successfully');
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccVoucherDeleteBulk");
    }
};
