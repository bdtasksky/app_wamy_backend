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
        DB::unprepared("CREATE PROCEDURE `AccVoucherPosting`(IN `jsonData` JSON, OUT `op_voucherNumber` VARCHAR(25), OUT `massage` VARCHAR(255))
BEGIN
    
    DECLARE voucher_id INT DEFAULT 0;
    DECLARE voucher_number VARCHAR(255);
    DECLARE voucher_date DATE;
    DECLARE company_id INT DEFAULT 0;
    DECLARE branch_id INT DEFAULT 0;
    DECLARE voucher_type_id INT DEFAULT 0;
    DECLARE voucher_event_code VARCHAR(25);
    DECLARE voucher_remarks VARCHAR(255);
    DECLARE created_by VARCHAR(100);
    DECLARE financialYearId INT;
    DECLARE dr_Amount DECIMAL(16,3);
    DECLARE cr_Amount DECIMAL(16,3);
    DECLARE voucher_master_id INT;
 DECLARE deletion_success TINYINT(1);
   DECLARE voucher_number_update VARCHAR(255);


    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        DROP TEMPORARY TABLE IF EXISTS jsonConvert_temp_table;
        SET massage= 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;
  
    
    CALL JsonConvert_Voucher(jsonData);

    
    SELECT VoucherId, VoucherNumber, VoucherDate, Companyid, BranchId, VoucherTypeId, VoucherEventCode, VoucherRemarks, Createdby
    INTO voucher_id, voucher_number, voucher_date, company_id, branch_id, voucher_type_id, voucher_event_code, voucher_remarks, created_by
    FROM jsonConvert_temp_table LIMIT 1; 

    
    IF EXISTS (SELECT * FROM acc_financialyear WHERE voucher_date BETWEEN start_date AND end_date AND is_active=1) THEN
        SELECT IFNULL(SUM(CAST(DrAmount AS DECIMAL(16,3))), 0), IFNULL(SUM(CAST(CrAmount AS DECIMAL(16,3))), 0) INTO dr_Amount, cr_Amount FROM jsonConvert_temp_table;
        
        
        IF (dr_Amount > 0.00 AND cr_Amount > 0.00) AND (dr_Amount = cr_Amount) THEN
            
            SELECT fiyear_id INTO financialYearId FROM acc_financialyear WHERE voucher_date BETWEEN start_date AND end_date AND is_active=1;
           
            IF voucher_id = 0 THEN
                
                INSERT INTO acc_voucher_master (VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate)
                VALUES (GetNewVoucherNumber(voucher_type_id,voucher_date), GetNewVoucherNumber(voucher_type_id,voucher_date), voucher_date, company_id, branch_id, financialYearId, voucher_type_id, voucher_event_code, dr_Amount, voucher_remarks, created_by, NOW());
                
                SET voucher_master_id = LAST_INSERT_ID();
                
                
                INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id, LaserComments, chequeno, chequeDate)
                SELECT voucher_master_id, acc_coa_id, IFNULL(CAST(DrAmount AS DECIMAL(16,3)), 0), IFNULL(CAST(CrAmount AS DECIMAL(16,3)), 0), subtype_id, subcode_id, LaserComments, chequeno, chequeDate
                FROM jsonConvert_temp_table;
                
                
                SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
                SET massage= CONCAT( op_voucherNumber,' Voucher is Saving Successfully');
                
			IF (SELECT approval_for_acc FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
			END IF;
                
            ELSE
              
			
			IF voucher_id > 0 AND (SELECT IsApprove FROM acc_voucher_master WHERE id = voucher_id) = FALSE THEN
			    
			    
			    SELECT RemoveVoucherDetails(voucher_id) INTO deletion_success;

			    
			    IF deletion_success = 1 THEN
				
				
	
	
                           SET voucher_number_update = CONCAT(
                                                            (SELECT PrefixCode FROM acc_vouchartype WHERE id = voucher_type_id),
							    '_',
							    DATE_FORMAT(voucher_date, '%y%m'),  
							    '_',
							    LPAD(SUBSTRING_INDEX((SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_id LIMIT 1), '_', -1) + 1, 6, '0')  
							);

			 UPDATE acc_voucher_master
			    SET 
				VoucherNumber = voucher_number_update,
				VoucherNumberMainBreanch=voucher_number_update,
				VoucherDate = voucher_date, 
				Companyid = company_id, 
				BranchId = branch_id, 
				FinancialYearId = financialYearId, 
				VoucharTypeId = voucher_type_id, 
				Voucher_event_code = voucher_event_code, 
				TranAmount = dr_Amount, 
				Remarks = voucher_remarks,  
				UpdatedBy = created_by, 
				UpdatedDate = NOW()
			    WHERE id = voucher_id; 
			    
			    
			    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id, LaserComments, chequeno, chequeDate)
			    SELECT voucher_id, acc_coa_id, 
				IFNULL(CAST(DrAmount AS DECIMAL(16,3)), 0), 
				IFNULL(CAST(CrAmount AS DECIMAL(16,3)), 0), 
				subtype_id, subcode_id, LaserComments, chequeno, chequeDate
			    FROM jsonConvert_temp_table;

			    
			    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_id LIMIT 1);
			    SET massage= CONCAT(op_voucherNumber ,' Voucher is Update Successfully');
				
				IF (SELECT approval_for_acc FROM setting LIMIT 1) = 1 THEN
				CALL AccVoucherApprove(voucher_id, @app_message);
				END IF;
				
			    ELSE
				
				SELECT 'Deletion failed or no rows affected' AS message;
			    END IF;		
			 ELSE
			      
				SET massage= 'Voucher is either not found or already approved.';
			END IF;

                
            END IF;
           
        ELSE
            
           SET massage= CONCAT('DrAmount: ', dr_Amount, ', CrAmount: ', cr_Amount,'  Dr and Cr amounts do not match');
        END IF; 
        
    ELSE
        
        
        SET massage= CONCAT('Voucher Date: ', voucher_date, ' is not within an active financial period.');
    END IF;

    
    DROP TEMPORARY TABLE IF EXISTS jsonConvert_temp_table;





    
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccVoucherPosting");
    }
};
