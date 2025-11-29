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
        DB::unprepared("CREATE PROCEDURE `AccIntegrationVoucherPosting`(IN `in_id` INT, IN `voucher_event_code` VARCHAR(25), OUT `message` VARCHAR(255))
BEGIN

    
    DECLARE v_bill_amount DECIMAL(15,2);
    DECLARE v_sales_amount DECIMAL(15,2);
    DECLARE v_saled_discount_amount DECIMAL(15,2);
    DECLARE v_vat_payable_amount DECIMAL(15,2);
    DECLARE v_service_amount DECIMAL(15,2);
    DECLARE v_deliverycharge_amount DECIMAL(15,2);
    DECLARE v_pm_amount DECIMAL(15,2);
    
    DECLARE v_p_total_price DECIMAL(15,2);
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
    DECLARE v_coa_PurchaseDiscount INT;

    
    DECLARE v_coa_PurchasesAcc INT;
    DECLARE v_coa_Supplier INT;
    



DECLARE v_coa_SalesReturn  INT;
DECLARE v_coa_SerciceReturn INT;

DECLARE v_sr_sub_total DECIMAL(15,2);
DECLARE v_sr_totalamount DECIMAL(15,2);
DECLARE v_sr_totaldiscount DECIMAL(15,2);
DECLARE v_sr_service_charge DECIMAL(15,2);
DECLARE v_sr_total_vat DECIMAL(15,2);
DECLARE v_sr_pay_amount DECIMAL(15,2);
DECLARE v_pay_amount DECIMAL(15,2);
DECLARE v_return_date DATE;


DECLARE return_order_vat DECIMAL(15,2);      
DECLARE v_return_amount  DECIMAL(15,2);

    
    DECLARE v_bill_date DATE;
    DECLARE v_create_by INT;
    DECLARE v_financialYearId INT;
    DECLARE v_subcode_id INT;
    DECLARE v_suplierID INT;
    DECLARE v_p_purchasedate DATE;
    
    
    
    DECLARE voucher_master_id INT;
    DECLARE voucher_type_id INT;
    DECLARE op_voucherNumber VARCHAR(250);
    DECLARE v_VoucherNumber VARCHAR(500);
    DECLARE v_p_paymenttype INT;
    DECLARE v_p_bankid INT;
    DECLARE v_sub_id INT;
    DECLARE sv_voucher_event_code VARCHAR(25);
    
     
    DECLARE v_coa_CommissionExp  INT;
    DECLARE v_coa_CommissionPayable  INT;
    DECLARE v_commission_amount DECIMAL(15,2);
    
    
    DECLARE v_coa_InventoryAdjustment  INT;
    DECLARE v_coa_Inventory INT;
           
 DECLARE v_adjustdate DATE;
 DECLARE v_adjustment_amount  DECIMAL(15,2);              
           
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;
     
     
     
     
     
     
        IF voucher_event_code = 'SPMS' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMS' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0 THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount , 0.00, 3, v_subcode_id);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount );

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
       
		    IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
			   

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
         IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
    
    ELSEIF voucher_event_code = 'SPMSD' THEN 
            
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSD' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, v_sales_amount, 0.00, 3, v_subcode_id);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
             IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
     ELSEIF voucher_event_code = 'SPMSV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount+v_vat_payable_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
           
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

          
           

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	    VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);
		    
            
            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
             IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
      
      
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'SPMSVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


           
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount,0.00);
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
             SET message = CONCAT(message, op_voucherNumber,',');
        
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
               SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
    
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'SPMSS' THEN 
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSS' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     ELSEIF voucher_event_code = 'SPMSDV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
                
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSDV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);
            
                 
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount); 

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    
       ELSEIF voucher_event_code = 'SPMSDVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSDVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount , CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_sales_amount, 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);
            

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
        
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount); 

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( message, op_voucherNumber,','); 
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
        
    
    
     ELSEIF voucher_event_code = 'SPMSSV' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);




            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'SPMSSVI' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

   

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


         
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber,','); 
       
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   
     ELSEIF voucher_event_code = 'SPMSSD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSD' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);



            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
         ELSEIF voucher_event_code = 'SPMSSDV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSDV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    ELSEIF voucher_event_code = 'SPMSSDVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSDVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

           

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);



            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
	    SET message = CONCAT(message, op_voucherNumber,',');
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
   

    ELSEIF voucher_event_code = 'MPMS' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMS' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0 THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' - POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount , 0.00, 3, v_subcode_id);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount );

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' - POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           
                
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
    
    ELSEIF voucher_event_code = 'MPMSD' THEN 
            
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSD' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, v_sales_amount, 0.00, 3, v_subcode_id);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
     ELSEIF voucher_event_code = 'MPMSV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
           
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);


           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
		
		
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
          IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'MPMSVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


           
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount,0.00);
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
             SET message = CONCAT(message, op_voucherNumber,',');
        
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
               SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'MPMSS' THEN 
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSS' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     ELSEIF voucher_event_code = 'MPMSDV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
                
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSDV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);
            
                 
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount); 

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    
       ELSEIF voucher_event_code = 'MPMSDVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSDVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount , CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_sales_amount, 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);
            

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
        
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount); 

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( message, op_voucherNumber,','); 
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
          IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
        
    
    
     ELSEIF voucher_event_code = 'MPMSSV' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);




            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'MPMSSVI' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

   

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   
     ELSEIF voucher_event_code = 'MPMSSD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSD' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);



            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
         ELSEIF voucher_event_code = 'MPMSSDV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSDV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    ELSEIF voucher_event_code = 'MPMSSDVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSDVI' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

           

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);



            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
	    SET message = CONCAT(message, op_voucherNumber,',');
	    
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
        

     
     



       ELSEIF voucher_event_code = 'DPMS' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1 AND b.voucher_event_code = 'DPMS' AND b.VoucherNumber IS NULL LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0 THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' - POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount , 0.00, 3, v_subcode_id);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount );

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            

            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
    
    
    
     ELSEIF voucher_event_code = 'DPMSV' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1  AND b.voucher_event_code = 'DPMSV' AND b.VoucherNumber IS NULL LIMIT 1;

	  
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
           
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);


           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

  IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;

            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'DPMSVI' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1  AND b.voucher_event_code = 'DPMSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	  

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_bill_amount+v_saled_discount_amount), 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

           
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount,0.00);
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
             SET message = CONCAT(message, op_voucherNumber,',');
        
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'DPMSS' THEN 
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1  AND b.voucher_event_code = 'DPMSS' AND b.VoucherNumber IS NULL LIMIT 1;

	
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
    
    
     ELSEIF voucher_event_code = 'DPMSSV' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1  AND b.voucher_event_code = 'DPMSSV' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);


            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            

            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'DPMSSVI' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0) ,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=1  AND b.voucher_event_code = 'DPMSSVI' AND b.VoucherNumber IS NULL LIMIT 1;

	  
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);

   

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

         
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber,','); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;



    
    
     IF v_commission_amount > 0.00 THEN
         

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_commission_amount, CONCAT(voucher_event_code,' POS Sales Commission Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_CommissionExp, v_commission_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, 0.00, v_commission_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(',', message, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
      END IF;
            
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=1  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   


    
    
    
    
    
    ELSEIF voucher_event_code = 'DPMS-SPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    

	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=2  AND b.voucher_event_code = 'DPMS-SPM' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0 THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' - POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


           

    IF v_commission_amount > 0.00 THEN
			 
    
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, (v_pm_amount) , 0.00);
    
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
 
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount , 0.00);
		   
    END IF;


  
    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
    VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount , 3, v_subcode_id);






            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);

            SET message = CONCAT(v_VoucherNumber , op_voucherNumber);
            
            
            
            
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
   
     ELSEIF voucher_event_code = 'DPMSV-SPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1  AND is_duepayment=2 AND b.voucher_event_code = 'DPMSV-SPM'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            



    IF v_commission_amount > 0.00 THEN
			 
    
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
          
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
		   
    END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'DPMSVI-SPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
            
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1  AND is_duepayment=2 AND b.voucher_event_code = 'DPMSVI-SPM'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_bill_amount+v_saled_discount_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


 IF v_commission_amount > 0.00 THEN
			 
    
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            
             IF v_saled_discount_amount > 0.00 THEN
             
             INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
             VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
             END IF;
            

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
          
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
             IF v_saled_discount_amount > 0.00 THEN
             
             INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
             VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
             END IF;
		   
    END IF;



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_bill_amount+v_saled_discount_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
               SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'DPMSS-SPM' THEN 
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=2  AND b.voucher_event_code = 'DPMSS-SPM'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

         


IF v_commission_amount > 0.00 THEN
			 
    
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
		   
    END IF;



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     
     ELSEIF voucher_event_code = 'DPMSSV-SPM' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=2  AND b.voucher_event_code = 'DPMSSV-SPM' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


   IF v_commission_amount > 0.00 THEN
			 
    
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
		   
    END IF;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'DPMSSVI-SPM' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=2  AND b.voucher_event_code = 'DPMSSVI-SPM' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

          


   IF v_commission_amount > 0.00 THEN
			 
    
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
    ELSE
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
    END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   
     
    
    
   
    
    
    ELSEIF voucher_event_code = 'DPMS-SPMD' THEN 
            
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND is_duepayment=2  AND b.voucher_event_code = 'DPMS-SPMD' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           



   IF v_commission_amount > 0.00 THEN
			 
    
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
            
            
    ELSE
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
    END IF;



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
     
    
     ELSEIF voucher_event_code = 'DPMSV-SPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1  AND is_duepayment=2  AND b.voucher_event_code = 'DPMSV-SPMD'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
           
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


    IF v_commission_amount > 0.00 THEN
			 
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
            
            
    ELSE
         
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
    END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    
       ELSEIF voucher_event_code = 'DPMSVI-SPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSVI-SPMD' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            

            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

         



 IF v_commission_amount > 0.00 THEN
			 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);
            
            
    ELSE
         
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
    END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
        
     ELSEIF voucher_event_code = 'DPMSS-SPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSS-SPMD'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


  IF v_commission_amount > 0.00 THEN
			 
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);
            

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);

    ELSE
         
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);
    END IF;




            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
         ELSEIF voucher_event_code = 'DPMSSV-SPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSSV-SPMD' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           








IF v_commission_amount > 0.00 THEN
			 
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);

    ELSE
         
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);
    END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(v_VoucherNumber, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    ELSEIF voucher_event_code = 'DPMSSVI-SPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1  AND is_duepayment=2  AND b.voucher_event_code = 'DPMSSVI-SPMD' LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount-v_saled_discount_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();


IF v_commission_amount > 0.00 THEN
			 
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_CommissionPayable, v_commission_amount , 0.00 , 3, v_subcode_id);

    ELSE
         
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);
    END IF;



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
    
    
    
    ELSEIF voucher_event_code = 'DPMS-MPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMS-MPM' LIMIT 1;

	   

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0 THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' - POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           
                
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
     ELSEIF voucher_event_code = 'DPMSV-MPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSV-MPM'  LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
           
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
		
		
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'DPMSVI-MPM' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
           
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSVI-MPM' LIMIT 1;

	    

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;



             IF v_saled_discount_amount > 0.00 THEN
             
             INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
             VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);
             END IF;


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_bill_amount+v_saled_discount_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
               SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'DPMSS-MPM' THEN 
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1  AND is_duepayment=2  AND b.voucher_event_code = 'DPMSS-MPM'  LIMIT 1;

	  
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'DPMSSV-MPM' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSSV-MPM' LIMIT 1;


            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    
     ELSEIF voucher_event_code = 'DPMSSVI-MPM' THEN  
      
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSSVI-MPM'  LIMIT 1;

	

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
           
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   

    
    

    
    ELSEIF voucher_event_code = 'DPMS-MPMD' THEN 
            
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,b.VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMS-MPMD'  LIMIT 1;

	
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     ELSEIF voucher_event_code = 'DPMSV-MPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,v_VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSV-MPMD'  LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    
       ELSEIF voucher_event_code = 'DPMSVI-MPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
   
        
          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
         
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,v_VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSVI-MPMD'  LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount, 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
        
    
     ELSEIF voucher_event_code = 'DPMSS-MPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,v_VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSS-MPMD'  LIMIT 1;

	 
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
           
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount), 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1   AND is_duepayment=2 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
         ELSEIF voucher_event_code = 'DPMSSV-MPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,v_VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSSV-MPMD' LIMIT 1;

	   
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

             
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
           IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    ELSEIF voucher_event_code = 'DPMSSVI-MPMD' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        
          
        SELECT acc_coa_id INTO v_coa_CommissionExp 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 34 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_CommissionPayable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 35 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id,v_VoucherNumber,IFNULL(b.commission_amount,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_VoucherNumber,v_commission_amount
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1   AND is_duepayment=2 AND b.voucher_event_code = 'DPMSSVI-MPMD'  LIMIT 1;

	  
            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_bill_amount IS NOT NULL THEN
            
            
            SET voucher_type_id = 2;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                GetNewVoucherNumber(voucher_type_id,v_bill_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            
                INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
		SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
		FROM multipay_bill m
		INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
		INNER JOIN acc_coas c ON c.id = p.acc_coa_id
		WHERE m.bill_id = in_id;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount) , 3, v_subcode_id);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(v_VoucherNumber, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1  AND is_duepayment=2  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     ELSEIF voucher_event_code LIKE '%PMP%' THEN
       
		CALL AccIntegrationVoucherPosting_Purchases(in_id, voucher_event_code, @output_message);
		
		SET message =  @output_message;
       
       

    ELSEIF voucher_event_code LIKE '%SR%' THEN
   
		CALL AccIntegrationVoucherPosting_SalesReturn(in_id, voucher_event_code, @output_message);
		
		SET message =@output_message;
		

        
   ELSEIF voucher_event_code = 'SALESSPLIT' THEN
         
         
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;

          
        SELECT acc_coa_id INTO v_coa_ServiceIncome 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =27 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
          
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;        
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),  b.bill_date, IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id 
	    INTO v_bill_amount,  v_bill_date, v_deliverycharge_amount, v_create_by, v_subcode_id
	    FROM bill b  
	    WHERE b.bill_id = in_id AND b.voucher_event_code = 'SALESSPLIT' LIMIT 1;


                    
		    SELECT b.sub_id, IFNULL(b.vat,0),IFNULL(b.discount,0),IFNULL(b.s_charge,0), IFNULL(b.total_price,0) , b.subcode_id,b.voucher_event_code
		    INTO  v_sub_id , v_vat_payable_amount,v_saled_discount_amount,v_service_amount,v_sales_amount,v_subcode_id, sv_voucher_event_code
		    FROM sub_order b 
		    WHERE b.order_id =in_id AND b.status = 1 AND b.voucher_event_code IS NOT NULL  AND b.VoucherNumber IS  NULL LIMIT 1;

			    SELECT fiyear_id INTO v_financialYearId
			    FROM acc_financialyear 
			    WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;
            

   
           IF v_financialYearId IS NOT NULL AND v_sales_amount IS NOT NULL THEN
           
           
                   IF sv_voucher_event_code LIKE '%VI%' THEN
                   
                   
			    SET voucher_type_id = 3;
			    INSERT INTO acc_voucher_master (
				VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
			    )
			    VALUES (
				GetNewVoucherNumber(voucher_type_id,v_bill_date), 
				GetNewVoucherNumber(voucher_type_id,v_bill_date), 
				v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount, CONCAT(voucher_event_code,' POS Split Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
			    );
			    SET voucher_master_id = LAST_INSERT_ID();

			   

			    
			    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
			    VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount, 0.00);

			     
			    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
			    VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);

			    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
			    SET message = CONCAT(message, op_voucherNumber,','); 
		       
			    IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
                            SET v_vat_payable_amount=0.00;
                   
                   END IF;
           
           
           
           
           
           
		    
		    SET voucher_type_id = 3;
		    INSERT INTO acc_voucher_master (
			VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
		    )
		    VALUES (
			GetNewVoucherNumber(voucher_type_id,v_bill_date), 
			GetNewVoucherNumber(voucher_type_id,v_bill_date), 
			v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Split Sales Order Number : ', in_id), v_create_by, NOW(), in_id
		    );
		    SET voucher_master_id = LAST_INSERT_ID();

		    
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
		    VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount), 0.00, 3, v_subcode_id);
		    
		    IF v_service_amount > 0.00 THEN 
		     
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
		    VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);
		     END IF;
		     
		    
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
		    VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount);
                    
                    IF v_vat_payable_amount > 0.00 THEN 
		     
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
		    VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount);
		    END IF;
		    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
		    SET message = CONCAT( op_voucherNumber,','); 
	       
		    IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;



		    
		    SET voucher_type_id = 2;
		    INSERT INTO acc_voucher_master (
			VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
		    )
		    VALUES (
			GetNewVoucherNumber(voucher_type_id,v_bill_date), 
			GetNewVoucherNumber(voucher_type_id,v_bill_date), 
			v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount), CONCAT(voucher_event_code,' POS Split Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
		    );
		    SET voucher_master_id = LAST_INSERT_ID();

		  
			INSERT INTO acc_voucher_details (voucher_master_id,  acc_coa_id, Dr_Amount, Cr_Amount)
			SELECT voucher_master_id,  p.acc_coa_id,m.amount, 0.00 
			FROM multipay_bill m
			INNER JOIN payment_method p ON m.payment_method_id = p.payment_method_id
			INNER JOIN acc_coas c ON c.id = p.acc_coa_id
			WHERE m.bill_id = in_id AND m.suborderid =v_sub_id; 
                    
                    IF v_saled_discount_amount > 0.00 THEN 
		    
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
		    VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);
		    END IF;

		    
		    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
		    VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount) , 3, v_subcode_id);

		    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
		    SET message = CONCAT(message, op_voucherNumber);
		    
		     IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


                    UPDATE sub_order  SET VoucherNumber = message WHERE sub_id=v_sub_id;
		    SELECT IFNULL(VoucherNumber,'') INTO v_VoucherNumber FROM bill  WHERE bill_id = 1 LIMIT 1;
		    UPDATE bill SET VoucherNumber = CONCAT(v_VoucherNumber, message) WHERE bill_id = in_id LIMIT 1;

		    SET message = CONCAT(message, ' Voucher Created Successfully'); 

		ELSE
		    SET message = 'VOUCHER NOT CREATED CHECK YOUR SALES SPLIT';
		END IF;   	
	    ELSE
		SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
	    END IF;
	    
	    
	    
	ELSEIF voucher_event_code = 'INVINC' THEN 
        
        SELECT acc_coa_id INTO v_coa_InventoryAdjustment 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 23 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        SELECT acc_coa_id INTO v_coa_Inventory 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =13 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

        IF v_coa_InventoryAdjustment IS NOT NULL AND v_coa_Inventory IS NOT NULL THEN
        	    
           SELECT ad.adjustdate,IFNULL(adjustment_amount,0),create_by INTO v_adjustdate ,v_adjustment_amount,v_create_by FROM  addjustmentitem ad WHERE ad.addjustid =in_id AND  ad.voucher_event_code = 'INVINC' AND ad.VoucherNumber IS NULL LIMIT 1;


            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_adjustdate BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_adjustment_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_adjustdate), 
                GetNewVoucherNumber(voucher_type_id,v_adjustdate), 
                v_adjustdate, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_adjustment_amount, CONCAT(voucher_event_code,' Inventory Adjustment Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_Inventory, v_adjustment_amount, 0.00);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_InventoryAdjustment, 0.00, v_adjustment_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,'.'); 
       
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            UPDATE addjustmentitem SET VoucherNumber = message WHERE addjustid = in_id  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR ADJUSTMENT';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'INVDEC' THEN 
        
        SELECT acc_coa_id INTO v_coa_InventoryAdjustment 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 23 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        SELECT acc_coa_id INTO v_coa_Inventory 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =13 AND p.is_active = TRUE AND ps.is_active = TRUE;
        

        IF v_coa_InventoryAdjustment IS NOT NULL AND v_coa_Inventory IS NOT NULL THEN
        	    
           SELECT ad.adjustdate,IFNULL(adjustment_amount,0),create_by INTO v_adjustdate ,v_adjustment_amount,v_create_by FROM  addjustmentitem ad WHERE ad.addjustid =in_id AND  ad.voucher_event_code = 'INVDEC' AND ad.VoucherNumber IS NULL LIMIT 1;


            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_adjustdate BETWEEN start_date AND end_date AND is_active = 1;
            
   
        IF v_financialYearId IS NOT NULL AND v_adjustment_amount IS NOT NULL THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_adjustdate), 
                GetNewVoucherNumber(voucher_type_id,v_adjustdate), 
                v_adjustdate, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_adjustment_amount, CONCAT(voucher_event_code,' Inventory Adjustment Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_InventoryAdjustment , v_adjustment_amount, 0.00);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_Inventory, 0.00, v_adjustment_amount);

            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,'.'); 
       
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;

            UPDATE addjustmentitem SET VoucherNumber = message WHERE addjustid = in_id  LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR ADJUSTMENT';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
	    
	    
        
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccIntegrationVoucherPosting");
    }
};
