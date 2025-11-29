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
        DB::unprepared("CREATE PROCEDURE `AccVoucherApprove`(IN `voucher_id` INT, OUT `message` VARCHAR(255))
BEGIN
    
    DECLARE voucher_number VARCHAR(255);
    DECLARE voucher_date DATE;
    DECLARE company_id INT;
    DECLARE branch_id INT;
    DECLARE voucher_type_id INT;
    DECLARE voucherEventCode VARCHAR(255);
    DECLARE voucher_remarks VARCHAR(255);
    DECLARE created_by VARCHAR(100);
    DECLARE create_date DATE;
    DECLARE financial_Year_Id INT;
    DECLARE purchase_ID INT;
    DECLARE bill_ID INT;
    DECLARE service_ID INT;
    DECLARE cr_det_amount DECIMAL(19, 3);
    DECLARE cr_acc_coa_id INT;
    DECLARE dr_det_amount DECIMAL(19, 3);
    DECLARE dr_acc_coa_id INT;
    DECLARE subtype_id_det INT;
    DECLARE subcode_id_det INT;
    DECLARE chequeno_det VARCHAR(191); 
    DECLARE chequeDate_det DATE;
    DECLARE ishonour_det TINYINT(1);
    DECLARE LaserComments_det VARCHAR(255);

DECLARE total_dr_amount DECIMAL(18,2);
DECLARE total_cr_amount DECIMAL(18,2);
DECLARE old_remarks VARCHAR(255);

	DECLARE DR_subtype_id INT;
	DECLARE DR_subcode_id INT;
	DECLARE CR_subtype_id INT;
	DECLARE CR_subcode_id INT;

    DECLARE done INT DEFAULT FALSE;
    
    DECLARE cur_CR CURSOR FOR
        SELECT Cr_Amount, acc_coa_id ,subtype_id, subcode_id, chequeno,IFNULL (chequeDate,'1900-01-01'), IFNULL (ishonour,0), LaserComments
        FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00;

    DECLARE cur_DR CURSOR FOR
        SELECT Dr_Amount, acc_coa_id ,subtype_id, subcode_id, chequeno,IFNULL (chequeDate,'1900-01-01'),  IFNULL (ishonour,0), LaserComments
        FROM acc_voucher_details  WHERE voucher_master_id =voucher_id  AND Dr_Amount > 0.00;

    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;



    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;



SET total_dr_amount = 0;
SET total_cr_amount = 0;
SET old_remarks = '';


SELECT SUM(Dr_Amount), SUM(Cr_Amount)
INTO total_dr_amount, total_cr_amount
FROM acc_voucher_details 
WHERE voucher_master_id = voucher_id;



    
    IF (SELECT COUNT(*) FROM acc_voucher_master WHERE id = voucher_id) = 0 THEN
        SET message = 'Voucher Number is Not Available';
    ELSEIF (total_dr_amount != total_cr_amount) THEN
     
    SELECT Remarks INTO old_remarks FROM acc_voucher_master WHERE id = voucher_id;
    
	    IF old_remarks NOT LIKE '***Error%' THEN
		    
		    UPDATE acc_voucher_master 
		    SET Remarks = CONCAT('***Error ', old_remarks)
		    WHERE id = voucher_id;
	    END IF;
     
    SET message = 'Voucher DR & CR Amount are Not Match';
    
    ELSE
        
        IF (SELECT COUNT(*) FROM acc_voucher_master WHERE id = voucher_id AND IsApprove = TRUE) = 1 THEN
            SET message = 'Voucher Already Approved';
        ELSE
            
            SELECT VoucherNumber, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, Remarks, Createdby, CreatedDate, PurchaseID, BillID, ServiceID
            INTO voucher_number, voucher_date, company_id, branch_id, financial_Year_Id, voucher_type_id, voucherEventCode, voucher_remarks, created_by, create_date, purchase_ID, bill_ID, service_ID
            FROM acc_voucher_master 
            WHERE id = voucher_id;


    
	    IF voucher_remarks LIKE '***Error%' THEN
		    
		    UPDATE acc_voucher_master 
		    SET Remarks = CONCAT(TRIM(LEADING '***Error ' FROM voucher_remarks))
		    WHERE id = voucher_id;
	    END IF;

            
            IF (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00) = 1 AND (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00) = 1 THEN
                
                INSERT INTO `acc_transactions` (
                    `voucher_master_id`, `FinancialYearId`, `VoucharTypeId`, `voucher_event_code`, `VoucherNumber`, `Remarks`, `VoucherDate`,`BranchId`,
                    `acc_coa_id`, `subtype_id`, `subcode_id`, `cheque_no`, `cheque_date`, `is_honour`, `ledger_comment`, `Dr_Amount`, `Cr_Amount`, `reverse_acc_coa_id`,
                    `PurchaseID`, `BillID`, `ServiceID`, `created_by`, `created_date`, `approved_by`, `approved_at`
                )
                SELECT voucher_id, financial_Year_Id, voucher_type_id, voucherEventCode, voucher_number, voucher_remarks, voucher_date,branch_id,
                    d.acc_coa_id, d.subtype_id, d.subcode_id, d.chequeno, IFNULL(d.chequeDate, '1900-01-01'), IFNULL(d.ishonour, 0), d.LaserComments,
                    d.Dr_Amount, d.Cr_Amount, 
                    (SELECT acc_coa_id FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00 LIMIT 1),
                    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()
                FROM acc_voucher_details AS d 
                WHERE d.voucher_master_id = voucher_id AND d.Dr_Amount > 0.00 
                LIMIT 1;
                
                
                INSERT INTO `acc_transactions` (
                    `voucher_master_id`, `FinancialYearId`, `VoucharTypeId`, `voucher_event_code`, `VoucherNumber`, `Remarks`, `VoucherDate`,`BranchId`,
                    `acc_coa_id`, `subtype_id`, `subcode_id`, `cheque_no`, `cheque_date`, `is_honour`, `ledger_comment`, `Dr_Amount`, `Cr_Amount`, `reverse_acc_coa_id`,
                    `PurchaseID`, `BillID`, `ServiceID`, `created_by`, `created_date`, `approved_by`, `approved_at`
                )
                SELECT voucher_id, financial_Year_Id, voucher_type_id, voucherEventCode, voucher_number, voucher_remarks, voucher_date,branch_id,
                    d.acc_coa_id, d.subtype_id, d.subcode_id, d.chequeno, IFNULL(d.chequeDate, '1900-01-01'), IFNULL(d.ishonour, 0), d.LaserComments,
                    d.Dr_Amount, d.Cr_Amount, 
                    (SELECT acc_coa_id FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00 LIMIT 1),
                    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()
                FROM acc_voucher_details AS d 
                WHERE d.voucher_master_id = voucher_id AND d.Cr_Amount > 0.00 
                LIMIT 1;
                
                
                UPDATE acc_voucher_master  SET `IsApprove` = 1, `Approvedby` = 1  WHERE id = voucher_id;
                
                
                SET message = 'Voucher Approved Successfully 1:1';
                
            ELSEIF (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00) = 1 
               AND (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00) > 1 THEN
               
               
		     SELECT acc_coa_id INTO dr_acc_coa_id FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00;
		     OPEN cur_CR;
			read_CR_loop: LOOP
			    FETCH cur_CR INTO cr_det_amount, cr_acc_coa_id,subtype_id_det, subcode_id_det, chequeno_det,chequeDate_det, ishonour_det, LaserComments_det;
			    IF done THEN LEAVE read_CR_loop; END IF;
			    
			    
                             SELECT subtype_id, subcode_id INTO DR_subtype_id, DR_subcode_id FROM acc_voucher_details  WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00;

				      
				    INSERT INTO `acc_transactions` ( `voucher_master_id`,`FinancialYearId`,`VoucharTypeId`,`voucher_event_code`,`VoucherNumber`,`Remarks`,`VoucherDate`,`BranchId`,
				    `acc_coa_id`,`subtype_id`,`subcode_id`,`cheque_no`, `cheque_date`,`is_honour`, `ledger_comment`,  `Dr_Amount`,`Cr_Amount`,`reverse_acc_coa_id`,
				      `PurchaseID`,`BillID`,`ServiceID`,`created_by`,created_date,`approved_by`,`approved_at`
				    ) VALUES ( voucher_id,financial_Year_Id,voucher_type_id,voucherEventCode,voucher_number,voucher_remarks,voucher_date, branch_id,
				    dr_acc_coa_id, DR_subtype_id, DR_subcode_id, chequeno_det, chequeDate_det, ishonour_det, LaserComments_det, cr_det_amount, 0.00, cr_acc_coa_id,
				    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()); 

				    
				    INSERT INTO `acc_transactions` ( `voucher_master_id`,`FinancialYearId`,`VoucharTypeId`,`voucher_event_code`,`VoucherNumber`,`Remarks`,`VoucherDate`,`BranchId`,
				    `acc_coa_id`,`subtype_id`,`subcode_id`,`cheque_no`, `cheque_date`,`is_honour`, `ledger_comment`,  `Dr_Amount`,`Cr_Amount`,`reverse_acc_coa_id`,
				      `PurchaseID`,`BillID`,`ServiceID`,`created_by`,created_date,`approved_by`,`approved_at`
				    ) VALUES ( voucher_id, financial_Year_Id, voucher_type_id, voucherEventCode, voucher_number, voucher_remarks, voucher_date, branch_id,
				    cr_acc_coa_id, subtype_id_det, subcode_id_det, chequeno_det, chequeDate_det, ishonour_det, LaserComments_det, 0.00, cr_det_amount, dr_acc_coa_id,
				    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()); 
				      
			END LOOP;
			CLOSE cur_CR;
			SET done = FALSE;               
                
                UPDATE acc_voucher_master  SET `IsApprove` = 1, `Approvedby` = 1  WHERE id = voucher_id;
                
                SET message = 'Voucher Approved Successfully DR(1) : CR(*)';
            ELSEIF (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00) = 1 
               AND (SELECT COUNT(*) FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Dr_Amount > 0.00) > 1 THEN
               
                       SELECT acc_coa_id INTO cr_acc_coa_id FROM acc_voucher_details WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00;
		       OPEN cur_DR;
			read_DR_loop: LOOP
			    FETCH cur_DR INTO dr_det_amount, dr_acc_coa_id,subtype_id_det, subcode_id_det, chequeno_det,chequeDate_det, ishonour_det, LaserComments_det;
			    IF done THEN LEAVE read_DR_loop; END IF;
			    
			    
			    
				
			    INSERT INTO `acc_transactions` ( `voucher_master_id`,`FinancialYearId`,`VoucharTypeId`,`voucher_event_code`,`VoucherNumber`,`Remarks`,`VoucherDate`,`BranchId`,
			    `acc_coa_id`,`subtype_id`,`subcode_id`,`cheque_no`, `cheque_date`,`is_honour`, `ledger_comment`,  `Dr_Amount`,`Cr_Amount`,`reverse_acc_coa_id`,
			      `PurchaseID`,`BillID`,`ServiceID`,`created_by`,created_date,`approved_by`,`approved_at`
			    ) VALUES ( voucher_id,financial_Year_Id,voucher_type_id,voucherEventCode,voucher_number,voucher_remarks,voucher_date, branch_id,
			    dr_acc_coa_id, subtype_id_det, subcode_id_det, chequeno_det, chequeDate_det, ishonour_det, LaserComments_det, dr_det_amount, 0.00,cr_acc_coa_id ,
			    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()); 

                            SELECT subtype_id, subcode_id INTO CR_subtype_id, CR_subcode_id FROM acc_voucher_details  WHERE voucher_master_id = voucher_id AND Cr_Amount > 0.00;
			    
			    INSERT INTO `acc_transactions` ( `voucher_master_id`,`FinancialYearId`,`VoucharTypeId`,`voucher_event_code`,`VoucherNumber`,`Remarks`,`VoucherDate`,`BranchId`,
			    `acc_coa_id`,`subtype_id`,`subcode_id`,`cheque_no`, `cheque_date`,`is_honour`, `ledger_comment`,  `Dr_Amount`,`Cr_Amount`,`reverse_acc_coa_id`,
			      `PurchaseID`,`BillID`,`ServiceID`,`created_by`,created_date,`approved_by`,`approved_at`
			    ) VALUES ( voucher_id, financial_Year_Id, voucher_type_id, voucherEventCode, voucher_number, voucher_remarks, voucher_date, branch_id,
			    cr_acc_coa_id, CR_subtype_id, CR_subcode_id, chequeno_det, chequeDate_det, ishonour_det, LaserComments_det, 0.00, dr_det_amount,dr_acc_coa_id ,
			    purchase_ID, bill_ID, service_ID, created_by, create_date, created_by, NOW()); 
				    
			END LOOP;
			CLOSE cur_DR;
			SET done = FALSE;               
                
                UPDATE acc_voucher_master  SET `IsApprove` = 1, `Approvedby` = 1  WHERE id = voucher_id;
                
                SET message = 'Voucher Approved Successfully CR(1) : DR(*)';
            END IF;
        END IF;
    END IF;

    
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccVoucherApprove");
    }
};
