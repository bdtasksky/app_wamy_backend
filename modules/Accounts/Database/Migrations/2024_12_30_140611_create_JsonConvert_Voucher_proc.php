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
        DB::unprepared("CREATE PROCEDURE `JsonConvert_Voucher`(IN `jsonData` JSON)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE max_elements INT;  
    DECLARE voucher_id INT DEFAULT 0;
    DECLARE voucher_number VARCHAR(255);
    DECLARE voucher_date DATE;
    DECLARE company_id INT DEFAULT 0;
    DECLARE branch_id INT DEFAULT 0;
    DECLARE voucher_type_id INT DEFAULT 0;
    DECLARE voucher_event_code VARCHAR(255);
    DECLARE voucher_remarks VARCHAR(255);
    DECLARE created_by VARCHAR(100);
    DECLARE acc_coa_id BIGINT UNSIGNED DEFAULT 0;
    DECLARE drAmount DECIMAL(16,3);
    DECLARE crAmount DECIMAL(16,3);
    DECLARE subtype_id INT UNSIGNED DEFAULT NULL;
    DECLARE subcode_id INT DEFAULT NULL;
    DECLARE laser_comments VARCHAR(250);
    DECLARE cheque_no VARCHAR(50);
    DECLARE cheque_date DATE;

    DROP TEMPORARY TABLE IF EXISTS jsonConvert_temp_table;

    CREATE TEMPORARY TABLE jsonConvert_temp_table (
        VoucherId INT DEFAULT 0,
        VoucherNumber VARCHAR(25) NULL,
        VoucherDate DATE NOT NULL,
        Companyid INT DEFAULT 0,
        BranchId INT DEFAULT 0,
        VoucherTypeId INT NOT NULL,
        VoucherEventCode VARCHAR(25) NOT NULL,
        VoucherRemarks VARCHAR(250) NOT NULL,
        Createdby VARCHAR(100) NOT NULL,
        acc_coa_id BIGINT(20) UNSIGNED NOT NULL,
        DrAmount  DECIMAL(16,3) NOT NULL,
        CrAmount  DECIMAL(16,3) NOT NULL,
        subtype_id INT(11) UNSIGNED DEFAULT NULL,
        subcode_id INT(11) DEFAULT NULL,
        LaserComments VARCHAR(250) NOT NULL,
        chequeno VARCHAR(50) DEFAULT NULL,
        chequeDate DATE DEFAULT NULL
    );
    SET max_elements = JSON_LENGTH(jsonData);
    WHILE i < max_elements DO
        SET voucher_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherId')));
        SET voucher_number = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherNumber')));
        SET voucher_date = STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherDate'))), '%Y-%m-%d');
        SET company_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].Companyid')));
        SET branch_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].BranchId')));
        SET voucher_type_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherTypeId')));
        SET voucher_event_code = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherEventCode')));
        SET voucher_remarks = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].VoucherRemarks')));
        SET created_by = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].Createdby')));
        SET acc_coa_id = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].acc_coa_id')));
        SET drAmount = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].DrAmount')));
        SET crAmount = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].CrAmount')));
        
        SET subtype_id = CASE
            WHEN JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].subtype_id'))) = '' THEN NULL
            ELSE JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].subtype_id')))
        END;
        
        SET subcode_id = CASE
            WHEN JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].subcode_id'))) = '' THEN NULL
            ELSE JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].subcode_id')))
        END;
 
        
        SET laser_comments = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].LaserComments')));
        SET cheque_no = JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].chequeno')));
        
        SET cheque_date = CASE
            WHEN JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].chequeDate'))) = '' THEN NULL
            ELSE STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(jsonData, CONCAT('$[', i, '].chequeDate'))), '%Y-%m-%d')
        END;

        IF voucher_number IS NOT NULL THEN
            INSERT INTO jsonConvert_temp_table (
               VoucherId, VoucherNumber, VoucherDate, Companyid, BranchId, VoucherTypeId, VoucherEventCode,
                VoucherRemarks, Createdby, acc_coa_id, DrAmount, CrAmount,
                subtype_id, subcode_id, LaserComments, chequeno, chequeDate
            )
            VALUES (
               voucher_id, voucher_number, voucher_date, company_id, branch_id, voucher_type_id, voucher_event_code,
                voucher_remarks, created_by, acc_coa_id, drAmount , crAmount , 
                subtype_id, subcode_id, laser_comments, cheque_no, cheque_date
            );
        END IF;

        SET i = i + 1;
    END WHILE;

  

    
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS JsonConvert_Voucher");
    }
};
