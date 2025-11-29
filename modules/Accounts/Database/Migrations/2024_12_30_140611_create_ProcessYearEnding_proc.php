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
        DB::unprepared("CREATE PROCEDURE `ProcessYearEnding`(IN `p_fiyear_id` INT, IN `p_branch_id` INT, IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_new_fy_title` NVARCHAR(25), IN `p_new_fy_start_date` DATE, IN `p_new_fy_end_date` DATE, IN `p_create_by` INT, OUT `message` NVARCHAR(500))
BEGIN
    DECLARE new_fiyear_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;
    
    
    SET message = 'ok-milton-21-11-24';
    START TRANSACTION;
     
     IF NOT EXISTS (SELECT * FROM acc_financialyear WHERE end_date > p_new_fy_start_date ) THEN
	    
	    IF EXISTS (SELECT * FROM acc_financialyear    WHERE fiyear_id = p_fiyear_id   AND is_active = 1    AND start_date = p_start_date    AND end_date = p_end_date LIMIT 1) THEN
		 
	              IF NOT EXISTS (SELECT * FROM acc_voucher_master    WHERE IsApprove !=1 AND FinancialYearId = p_fiyear_id  AND BranchId= p_branch_id  AND VoucherDate BETWEEN p_start_date AND p_end_date) THEN	  
			  
			  SET @is_equal = FALSE;  
			  CALL GetTrilBalanceCheck(p_branch_id, p_start_date, p_end_date, @is_equal);
			  IF @is_equal = 1 THEN
			            
			          
			  
				   
				  DELETE FROM acc_openingbalance WHERE financial_year_id=p_fiyear_id;
				  
				   
				  INSERT INTO acc_openingbalance(financial_year_id, acc_coa_id,account_code,debit,credit,open_date,acc_subtype_id,acc_subcode_id,created_by,  created_at)
				  SELECT p_fiyear_id, acc_id ,account_code,debit,credit,p_end_date, subtype_id,subcode_id,p_create_by,NOW() FROM temp_table_ye;
				       

				       
				       
				       
					SELECT acc_coa_id  INTO @v_coa_LastYearProfitLoss
					FROM acc_predefined p	
					INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
					WHERE p.id = 15 AND p.is_active = TRUE AND ps.is_active = TRUE;
					
					SELECT account_code INTO @account_code FROM acc_coas WHERE id=@v_coa_LastYearProfitLoss;
				
					CALL GetProfitLoassAmount(p_branch_id, p_start_date, p_end_date, @profit_loss);
					
					
					
					IF @profit_loss > 0 THEN
					    
					    UPDATE acc_openingbalance SET credit = credit + ABS(@profit_loss) WHERE financial_year_id=p_fiyear_id AND acc_coa_id=@v_coa_LastYearProfitLoss;
					ELSE
					    
					    UPDATE acc_openingbalance SET debit = debit + ABS(@profit_loss) WHERE financial_year_id=p_fiyear_id AND acc_coa_id=@v_coa_LastYearProfitLoss;
					END IF;
					

					DROP TEMPORARY TABLE IF EXISTS temp_table_ye;
				  
					
					
					SET @insertTableName = CONCAT('acc_transactions', p_fiyear_id); 
					SET @deleteSQL = CONCAT('DROP TABLE IF EXISTS ', @insertTableName);
					PREPARE stmt FROM @deleteSQL;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					SET @insertTableName = CONCAT('acc_voucher_details', p_fiyear_id); 
					SET @deleteSQL = CONCAT('DROP TABLE IF EXISTS ', @insertTableName);
					PREPARE stmt FROM @deleteSQL;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					SET @insertTableName = CONCAT('acc_voucher_master', p_fiyear_id);
					SET @deleteSQL = CONCAT('DROP TABLE IF EXISTS ', @insertTableName);
					PREPARE stmt FROM @deleteSQL;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
							 
							 
							SET @tableName = CONCAT( 'acc_voucher_master',p_fiyear_id);
							SET @columnDefinitions = '(
							  `id` INT,
							  `VoucherNumber` VARCHAR(255) NOT NULL,
							  `VoucherNumberMainBreanch` VARCHAR(255) DEFAULT NULL,
							  `VoucherDate` DATE NOT NULL,
							  `Companyid` INT DEFAULT \'0\',
							  `BranchId` INT DEFAULT \'0\',
							  `FinancialYearId` INT NOT NULL,
							  `VoucharTypeId` INT NOT NULL,
							  `Voucher_event_code` VARCHAR(25) NOT NULL,
							  `TranAmount` DECIMAL(10,2) NOT NULL,
							  `Remarks` TEXT,
							  `Createdby` VARCHAR(100) NOT NULL,
							  `CreatedDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  `UpdatedBy` VARCHAR(100) DEFAULT NULL,
							  `UpdatedDate` DATETIME NOT NULL DEFAULT \'1970-01-01 00:00:00\',
							  `IsApprove` TINYINT(1) NOT NULL DEFAULT \'0\',
							  `Approvedby` VARCHAR(100) DEFAULT NULL,
							  `Approvedate` DATETIME NOT NULL DEFAULT \'1970-01-01 00:00:00\',
							  `PurchaseID` INT DEFAULT NULL,
							  `BillID` BIGINT DEFAULT NULL,
							  `ServiceID` INT DEFAULT NULL,
							  `IsYearClosed` TINYINT(1) NOT NULL DEFAULT \'0\',
							  `preturn_id` INT DEFAULT NULL,
							  `oreturn_id` INT DEFAULT NULL,
							
							  KEY `Companyid` (`Companyid`),
							  KEY `BranchId` (`BranchId`),
							  KEY `FinancialYearId` (`FinancialYearId`),
							  KEY `VoucharTypeId` (`VoucharTypeId`),
							  KEY `BillID` (`BillID`),
							  KEY `PurchaseID` (`PurchaseID`),
							  KEY `Voucher_event_code` (`Voucher_event_code`),
							  KEY `fk_preturn_id_purchase_return` (`preturn_id`),
							  KEY `fk_oreturn_id_sale_return` (`oreturn_id`),
							  FOREIGN KEY (`BillID`) REFERENCES `bill` (`bill_id`),
							  FOREIGN KEY (`FinancialYearId`) REFERENCES `acc_financialyear` (`fiyear_id`),
							  FOREIGN KEY (`oreturn_id`) REFERENCES `sale_return` (`oreturn_id`),
							  FOREIGN KEY (`preturn_id`) REFERENCES `purchase_return` (`preturn_id`),
							  FOREIGN KEY (`PurchaseID`) REFERENCES `purchaseitem` (`purID`),
							  FOREIGN KEY (`VoucharTypeId`) REFERENCES `acc_vouchartype` (`id`),
							  FOREIGN KEY (`Voucher_event_code`) REFERENCES `acc_voucher_event` (`voucher_event_code`)
							)';

							SET @engine = 'InnoDB';
							SET @charset = 'utf8mb3';
							SET @sql = CONCAT(
							  'CREATE TABLE IF NOT EXISTS ', @tableName, ' ', @columnDefinitions, 
							  ' ENGINE=', @engine, ' DEFAULT CHARSET=', @charset
							);
							
							PREPARE stmt FROM @sql;
							EXECUTE stmt;
							
							DEALLOCATE PREPARE stmt;

							
							
							SET @tableName = CONCAT('acc_voucher_details', p_fiyear_id);
							SET @columnDefinitions = CONCAT('(
							  `id` BIGINT,
							  `voucher_master_id` INT NOT NULL,
							  `acc_coa_id` BIGINT UNSIGNED NOT NULL,
							  `Dr_Amount` DECIMAL(19,3) NOT NULL DEFAULT 0.000,
							  `Cr_Amount` DECIMAL(19,3) NOT NULL DEFAULT 0.000,
							  `subtype_id` INT UNSIGNED DEFAULT NULL,
							  `subcode_id` INT DEFAULT NULL,
							  `LaserComments` TEXT,
							  `chequeno` VARCHAR(50) DEFAULT NULL,
							  `chequeDate` DATE DEFAULT NULL,
							  `ishonour` BIT(1) DEFAULT NULL,
						
							  KEY `acc_coa_id` (`acc_coa_id`),
							  KEY `acc_voucher_details_subtype_id` (`subtype_id`),
							  KEY `acc_voucher_details_subcode_id` (`subcode_id`),
							  
							  FOREIGN KEY (`acc_coa_id`) REFERENCES `acc_coas` (`id`),
							  FOREIGN KEY (`subtype_id`) REFERENCES `acc_subtype` (`id`)
							)');

							SET @engine = 'InnoDB';
							SET @charset = 'utf8mb3';

							
							SET @sql = CONCAT(
							  'CREATE TABLE IF NOT EXISTS ', @tableName, ' ', @columnDefinitions, 
							  ' ENGINE=', @engine, ' DEFAULT CHARSET=', @charset
							);

							
							PREPARE stmt FROM @sql;
							EXECUTE stmt;

							
							DEALLOCATE PREPARE stmt;

							
							
							SET @tableName = CONCAT('acc_transactions', p_fiyear_id);
							SET @columnDefinitions = CONCAT('(
							  `id` BIGINT,
							  `voucher_master_id` INT NOT NULL,
							  `Companyid` INT DEFAULT 0,
							  `BranchId` INT DEFAULT 0,
							  `FinancialYearId` INT NOT NULL,
							  `VoucharTypeId` INT NOT NULL,
							  `voucher_event_code` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
							  `VoucherNumber` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
							  `Remarks` TEXT COLLATE utf8mb4_unicode_ci,
							  `VoucherDate` DATE NOT NULL,
							  `acc_coa_id` BIGINT UNSIGNED NOT NULL,
							  `subtype_id` INT UNSIGNED DEFAULT NULL,
							  `subcode_id` INT DEFAULT NULL,
							  `cheque_no` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
							  `cheque_date` DATE DEFAULT NULL,
							  `is_honour` TINYINT(1) NOT NULL DEFAULT 0,
							  `ledger_comment` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
							  `Dr_Amount` DECIMAL(19,3) NOT NULL DEFAULT 0.000,
							  `Cr_Amount` DECIMAL(19,3) NOT NULL DEFAULT 0.000,
							  `reverse_acc_coa_id` BIGINT UNSIGNED NOT NULL,
							  `PurchaseID` INT DEFAULT NULL,
							  `BillID` BIGINT DEFAULT NULL,
							  `ServiceID` INT DEFAULT NULL,
							  `IsYearClosed` TINYINT(1) NOT NULL DEFAULT 0,
							  `created_by` BIGINT UNSIGNED DEFAULT NULL,
							  `created_date` DATE NOT NULL,
							  `approved_by` BIGINT UNSIGNED DEFAULT NULL,
							  `approved_at` TIMESTAMP NULL DEFAULT NULL,
						
							  KEY `Companyid` (`Companyid`),
							  KEY `BranchId` (`BranchId`),
							  KEY `subtype_id` (`subtype_id`),
							  KEY `subcode_id` (`subcode_id`),
							  KEY `acc_coa_id` (`acc_coa_id`),
							  KEY `reverse_acc_coa_id` (`reverse_acc_coa_id`),
							  KEY `BillID` (`BillID`),
							  KEY `PurchaseID` (`PurchaseID`),
							  KEY `VoucharTypeId` (`VoucharTypeId`),
							  KEY `FinancialYearId` (`FinancialYearId`),
							  FOREIGN KEY (`acc_coa_id`) REFERENCES `acc_coas` (`id`),
							  FOREIGN KEY (`BillID`) REFERENCES `bill` (`bill_id`),
							  FOREIGN KEY (`FinancialYearId`) REFERENCES `acc_financialyear` (`fiyear_id`),
							  FOREIGN KEY (`PurchaseID`) REFERENCES `purchaseitem` (`purID`),
							  FOREIGN KEY (`reverse_acc_coa_id`) REFERENCES `acc_coas` (`id`),
							  FOREIGN KEY (`subtype_id`) REFERENCES `acc_subtype` (`id`),
							  FOREIGN KEY (`VoucharTypeId`) REFERENCES `acc_vouchartype` (`id`)
							  
							)');

							SET @engine = 'InnoDB';
							SET @charset = 'utf8mb4';

							
							SET @sql = CONCAT(
							  'CREATE TABLE IF NOT EXISTS ', @tableName, ' ', @columnDefinitions, 
							  ' ENGINE=', @engine, ' DEFAULT CHARSET=', @charset
							);

							
							PREPARE stmt FROM @sql;
							EXECUTE stmt;

							
							DEALLOCATE PREPARE stmt;  

											
											
											
											SET @insertTableName = CONCAT('acc_voucher_master', p_fiyear_id); 
											SET @sourceTableName = CONCAT('acc_voucher_master WHERE VoucherDate BETWEEN ''',p_start_date, ''' AND ''', p_end_date, '''');

											
											SET @insertSQL = CONCAT(
											  'INSERT INTO ', @insertTableName, ' (',
											  'id, ',
											  'VoucherNumber, ',
											  'VoucherNumberMainBreanch, ',
											  'VoucherDate, ',
											  'Companyid, ',
											  'BranchId, ',
											  'FinancialYearId, ',
											  'VoucharTypeId, ',
											  'Voucher_event_code, ',
											  'TranAmount, ',
											  'Remarks, ',
											  'Createdby, ',
											  'CreatedDate, ',
											  'UpdatedBy, ',
											  'UpdatedDate, ',
											  'IsApprove, ',
											  'Approvedby, ',
											  'Approvedate, ',
											  'PurchaseID, ',
											  'BillID, ',
											  'ServiceID, ',
											  'IsYearClosed, ',
											  'preturn_id, ',
											  'oreturn_id) ',
											  'SELECT ',
											  'id,',
											  'VoucherNumber, ',
											  'VoucherNumberMainBreanch, ',
											  'VoucherDate, ',
											  'Companyid, ',
											  'BranchId, ',
											  'FinancialYearId, ',
											  'VoucharTypeId, ',
											  'Voucher_event_code, ',
											  'TranAmount, ',
											  'Remarks, ',
											  'Createdby, ',
											  'CreatedDate, ',
											  'UpdatedBy, ',
											  'UpdatedDate, ',
											  'IsApprove, ',
											  'Approvedby, ',
											  'Approvedate, ',
											  'PurchaseID, ',
											  'BillID, ',
											  'ServiceID, ',
											  '1, ',
											  'preturn_id, ',
											  'oreturn_id ',
											  'FROM ', @sourceTableName
											);

											
											PREPARE stmt FROM @insertSQL;
											EXECUTE stmt;

											
											DEALLOCATE PREPARE stmt;

											
											
											SET @insertTableName = CONCAT('acc_voucher_details', p_fiyear_id); 
											SET @sourceTableName = CONCAT('acc_voucher_master m INNER JOIN acc_voucher_details d ON m.id = d.voucher_master_id WHERE m.VoucherDate BETWEEN ''', p_start_date, ''' AND ''', p_end_date, '''');
											SET @insertSQL = CONCAT(
											  'INSERT INTO ', @insertTableName, ' (',
											  'id, ',
											  'voucher_master_id, ',
											  'acc_coa_id, ',
											  'Dr_Amount, ',
											  'Cr_Amount, ',
											  'subtype_id, ',
											  'subcode_id, ',
											  'LaserComments, ',
											  'chequeno, ',
											  'chequeDate, ',
											  'ishonour) ',
											  'SELECT ',
											  'd.id, ',
											  'd.voucher_master_id, ',
											  'd.acc_coa_id, ',
											  'd.Dr_Amount, ',
											  'd.Cr_Amount, ',
											  'd.subtype_id, ',
											  'd.subcode_id, ',
											  'd.LaserComments, ',
											  'd.chequeno, ',
											  'd.chequeDate, ',
											  'd.ishonour ',
											  'FROM ', @sourceTableName
											);
											
											PREPARE stmt FROM @insertSQL;
											EXECUTE stmt;
											
											DEALLOCATE PREPARE stmt;
											

											
											
											SET @insertTableName = CONCAT('acc_transactions', p_fiyear_id); 
											SET @sourceTableName = CONCAT('acc_voucher_master m INNER JOIN acc_transactions t ON m.id = t.voucher_master_id WHERE m.VoucherDate BETWEEN ''', p_start_date, ''' AND ''', p_end_date, '''');
											SET @insertSQL = CONCAT(
											  'INSERT INTO ', @insertTableName, ' (',
											  'id, ',
											  'voucher_master_id, Companyid, BranchId, FinancialYearId, VoucharTypeId, ',
											  'voucher_event_code, VoucherNumber, Remarks, VoucherDate, acc_coa_id, ',
											  'subtype_id, subcode_id, cheque_no, cheque_date, is_honour, ',
											  'ledger_comment, Dr_Amount, Cr_Amount, reverse_acc_coa_id, ',
											  'PurchaseID, BillID, ServiceID, IsYearClosed, created_by, created_date, ',
											  'approved_by, approved_at) ',
											  'SELECT ',
											  't.id, ',
											  't.voucher_master_id, t.Companyid, t.BranchId, t.FinancialYearId, t.VoucharTypeId, ',
											  't.voucher_event_code, t.VoucherNumber, t.Remarks, t.VoucherDate, t.acc_coa_id, ',
											  't.subtype_id, t.subcode_id, t.cheque_no, t.cheque_date, t.is_honour, ',
											  't.ledger_comment, t.Dr_Amount, t.Cr_Amount, t.reverse_acc_coa_id, ',
											  't.PurchaseID, t.BillID, t.ServiceID, t.IsYearClosed, t.created_by, t.created_date, ',
											  't.approved_by, t.approved_at ',
											  'FROM ', @sourceTableName
											);

											
											PREPARE stmt FROM @insertSQL;
											EXECUTE stmt;

											
											DEALLOCATE PREPARE stmt; 
													
												 
												    
												  DELETE FROM acc_transactions WHERE VoucherDate BETWEEN p_start_date AND p_end_date;
												  DELETE FROM acc_voucher_details WHERE voucher_master_id IN (SELECT id FROM acc_voucher_master WHERE VoucherDate BETWEEN p_start_date AND p_end_date);
												  DELETE FROM acc_voucher_master WHERE VoucherDate BETWEEN p_start_date AND p_end_date;
													 
													 
													
													INSERT INTO acc_financialyear (title,start_date, end_date, date_time ,is_active,create_by)
													VALUES(p_new_fy_title,p_new_fy_start_date,p_new_fy_end_date,NOW(),1,p_create_by);

															 
															 UPDATE acc_financialyear   SET is_active = 2  WHERE fiyear_id = p_fiyear_id   AND is_active = 1  AND start_date = p_start_date   AND end_date = p_end_date;
															       
															       
															       
															       SELECT fiyear_id INTO new_fiyear_id FROM acc_financialyear  WHERE  is_active = 1  AND start_date = p_new_fy_start_date   AND end_date = p_new_fy_end_date LIMIT 1;
															       UPDATE acc_voucher_master SET FinancialYearId = new_fiyear_id WHERE VoucherDate BETWEEN p_new_fy_start_date AND p_new_fy_end_date;
															       UPDATE acc_transactions SET FinancialYearId = new_fiyear_id WHERE VoucherDate BETWEEN p_new_fy_start_date AND p_new_fy_end_date;
																       
																       
																	SET message = CONCAT('Year Ending Complete Successfully ', p_new_fy_title ,' New Accounting Book has been Opening');
					
		
			  ELSE
			  SET message = 'Trial Balance Not Match';
			  END IF;
		   ELSE
	            SET message = 'All Voucher is not Aproval';
	           END IF;
	    ELSE
		SET message = 'Financial Year is not Active';
	    END IF;
    
     ELSE
	SET message = 'Financial Year Opening date mast be Greater then Previous Year Date';
     END IF;
    
    

    
    COMMIT;

    
    SELECT message;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS ProcessYearEnding");
    }
};
