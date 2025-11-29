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
        DB::unprepared("CREATE DEFINER=`remote`@`%` PROCEDURE `AccIntegrationVoucherPosting_Purchases`(IN `in_id` INT, IN `voucher_event_code` VARCHAR(25), OUT `message` VARCHAR(255))
BEGIN

    
    DECLARE v_bill_amount DECIMAL(15,2);
    DECLARE v_sales_amount DECIMAL(15,2);
    DECLARE v_saled_discount_amount DECIMAL(15,2);
    DECLARE v_vat_payable_amount DECIMAL(15,2);
    DECLARE v_service_amount DECIMAL(15,2);
    DECLARE v_deliverycharge_amount DECIMAL(15,2);
    DECLARE v_pm_amount DECIMAL(15,2);
    
    DECLARE v_p_sub_total DECIMAL(15,2);
    DECLARE v_p_paid_amount DECIMAL(15,2);
    DECLARE v_p_vat DECIMAL(15,2);
    DECLARE v_p_discount DECIMAL(15,2);
    DECLARE v_p_transpostcost DECIMAL(15,2);
    DECLARE v_p_labourcost DECIMAL(15,2);
    DECLARE v_p_othercost DECIMAL(15,2);
   

    
    DECLARE v_coa_Customer INT;
    DECLARE v_coa_SalesAcc INT;
    DECLARE v_coa_SalesDiscount INT;
    DECLARE v_coa_vat_payable INT;
    DECLARE v_coa_ServiceIncome INT;
    DECLARE v_coa_Deliverycharge INT;
    DECLARE v_coa_PaymentMethod INT;
    DECLARE v_coa_Carrying INT;
    DECLARE v_coa_Labor INT;
    DECLARE v_coa_OthersCost INT;
    DECLARE v_coa_VatPayable INT;
    DECLARE v_coa_VATReceivable INT;
    DECLARE v_coa_PurchaseDiscount INT;

    
    DECLARE v_coa_PurchasesAcc INT;
    DECLARE v_coa_Supplier INT;

    
    
    
    
    DECLARE v_pr_totalamount  DECIMAL(15,2);
    DECLARE v_pr_vat  DECIMAL(15,2);
    DECLARE v_pr_discount  DECIMAL(15,2);
    DECLARE v_pr_return_date DATE;
    DECLARE v_pr_acc_coa_id INT;
    DECLARE v_coa_PurchaseReturnAcc INT;
    
    
    
    DECLARE v_bill_date DATE;
    DECLARE v_create_by INT;
    DECLARE v_financialYearId INT;
    DECLARE v_customer_id INT;
    DECLARE v_subcode_id INT;
    DECLARE v_p_purchasedate DATE;
    DECLARE v_branch_id INT;
    
    
    DECLARE voucher_master_id INT;
    DECLARE voucher_type_id INT;
    DECLARE op_voucherNumber VARCHAR(250);
    DECLARE v_VoucherNumber VARCHAR(500);
    DECLARE v_p_paymenttype INT;
    DECLARE v_p_acc_coa_id INT;
           
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;


        SELECT acc_coa_id INTO v_coa_PurchasesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 4 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Supplier 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 9 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_Carrying
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 30 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_Labor 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 31 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_OthersCost
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 32 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_VatPayable
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_VATReceivable
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 17 AND p.is_active = TRUE AND ps.is_active = TRUE;
         
         
        SELECT acc_coa_id INTO v_coa_PurchaseDiscount
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 5 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

IF voucher_event_code = 'SPMP' THEN 
      
        IF v_coa_PurchasesAcc IS NOT NULL AND v_coa_Supplier IS NOT NULL THEN	

	-- main	    
	SELECT  IFNULL(p.sub_total,0),IFNULL(p.paid_amount,0),IFNULL(p.total_vat_amount,0),IFNULL(p.product_discount,0),IFNULL(p.transpostcost,0),IFNULL(p.labourcost,0),IFNULL(p.othercost,0),p.purchase_date,p.payment_type,p.create_by,p.subcode_id,p.branch_id
	INTO  v_p_sub_total,v_p_paid_amount,v_p_vat,v_p_discount,v_p_transpostcost,v_p_labourcost,v_p_othercost,v_p_purchasedate,v_p_paymenttype,v_create_by,v_subcode_id ,v_branch_id
	FROM  purchases p 
	WHERE p.id =in_id AND p.voucher_event_code = 'SPMP' AND p.VoucherNumber IS NULL LIMIT 1;
	    
	    
	    
	    
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_p_purchasedate BETWEEN start_date AND end_date AND is_active = 1;

        IF v_financialYearId IS NOT NULL AND v_p_sub_total > 0 THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, 
                VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, PurchaseID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                v_p_purchasedate, 0, v_branch_id , v_financialYearId, voucher_type_id, voucher_event_code, (v_p_sub_total+v_p_transpostcost+v_p_labourcost+v_p_othercost+v_p_vat), CONCAT(voucher_event_code,' Manage Purchases Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();



            IF v_p_sub_total > 0.00 THEN 
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PurchasesAcc, v_p_sub_total, 0.00);
            END IF;
             
            IF v_p_transpostcost > 0.00 THEN 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_Carrying, v_p_transpostcost, 0.00);
            END IF;
            
            IF v_p_labourcost > 0.00 THEN 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_Labor, v_p_labourcost, 0.00);
            END IF;
            
            IF v_p_othercost > 0.00 THEN 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_OthersCost, v_p_othercost, 0.00);
            END IF;
            
            IF v_p_vat > 0.00 THEN 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_VatPayable, v_p_vat, 0.00);            
             END IF;

            IF (v_p_sub_total+v_p_transpostcost+v_p_labourcost+v_p_othercost+v_p_vat) > 0.00 THEN 
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Supplier, 0.00, (v_p_sub_total+v_p_transpostcost+v_p_labourcost+v_p_othercost+v_p_vat) ,  4, v_subcode_id);
            END IF;
            

            

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
  
            
             IF (SELECT approval_for_purchase_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            

            
            SET voucher_type_id = 1;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, PurchaseID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                v_p_purchasedate, 0, v_branch_id, v_financialYearId, voucher_type_id, voucher_event_code, (v_p_sub_total+v_p_transpostcost+v_p_labourcost+v_p_othercost+v_p_vat), CONCAT(voucher_event_code,'  Manage Payment Purchases Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Supplier, v_p_sub_total, 0.00 , 4, v_subcode_id);

	    INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
            SELECT  voucher_master_id,p.payment_id AS acc_coa_id,p.payment_value, 0.00  FROM purchase_payments p
            WHERE p.purchase_id = in_id;
 

                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.payment_value, 0.00 
		FROM purchase_payments m
		INNER JOIN payment_method p ON m.payment_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.purchase_id = in_id;

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_purchase_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            UPDATE purchases SET VoucherNumber = message WHERE id =in_id  LIMIT 1; 
            
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR PURCHASES ';
        END IF;
    ELSE
        SET message = 'PURCHASES/SUPPLIER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    


ELSEIF voucher_event_code = 'DPMP' THEN 
        
        IF v_coa_PurchasesAcc IS NOT NULL AND v_coa_Supplier IS NOT NULL THEN	
  
	 -- main	    
	SELECT  IFNULL(p.sub_total,0),IFNULL(p.paid_amount,0),IFNULL(p.total_vat_amount,0),IFNULL(p.product_discount,0),IFNULL(p.transpostcost,0),IFNULL(p.labourcost,0),IFNULL(p.othercost,0),p.purchase_date,p.payment_type,p.create_by,p.subcode_id,p.branch_id
	INTO  v_p_sub_total,v_p_paid_amount,v_p_vat,v_p_discount,v_p_transpostcost,v_p_labourcost,v_p_othercost,v_p_purchasedate,v_p_paymenttype,v_create_by,v_subcode_id ,v_branch_id
	FROM  purchases p 
	WHERE p.id =in_id AND p.voucher_event_code = 'DPMP' AND p.VoucherNumber IS NULL LIMIT 1;
	    

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_p_purchasedate BETWEEN start_date AND end_date AND is_active = 1;

        IF v_financialYearId IS NOT NULL AND v_p_sub_total > 0 THEN
            
            SET voucher_type_id = 3;
            
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, 
                VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, PurchaseID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                GetNewVoucherNumber(voucher_type_id,v_p_purchasedate), 
                v_p_purchasedate, 0, v_branch_id, v_financialYearId, voucher_type_id, voucher_event_code, v_p_sub_total, CONCAT(voucher_event_code,' POS Purchases Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PurchasesAcc, v_p_sub_total, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Supplier, 0.00, v_p_sub_total ,  4, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_purchase_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
    
           UPDATE purchases SET VoucherNumber = message WHERE id =in_id  LIMIT 1; 
            
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR PURCHASES aaaa';
        END IF;
    ELSE
        SET message = 'PURCHASES/SUPPLIER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     ELSEIF voucher_event_code = 'ACC2' THEN
        SET message = voucher_event_code;
     ELSEIF voucher_event_code = 'ACC1' THEN
        SET message = voucher_event_code;
    ELSEIF voucher_event_code = 'ACC' THEN
        SET message = voucher_event_code;
    ELSE
        SET message = 'UNKNOWN_EVENT_CODE';
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccIntegrationVoucherPosting_Purchases");
    }
};
