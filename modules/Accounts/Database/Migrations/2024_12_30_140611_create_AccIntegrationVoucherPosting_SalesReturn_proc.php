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
        DB::unprepared("CREATE PROCEDURE `AccIntegrationVoucherPosting_SalesReturn`(IN `in_id` INT, IN `voucher_event_code` VARCHAR(25), OUT `message` VARCHAR(255))
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
    DECLARE v_return_order_id  INT;

    
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


DECLARE v_return_order_vat_amount DECIMAL(15,2);      
DECLARE v_return_amount  DECIMAL(15,2);
DECLARE v_return_order_total_amount  DECIMAL(15,2);
DECLARE v_return_service_amount DECIMAL(15,2);
    
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
           
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET message = 'ROLLBACK';
        ROLLBACK;
        RESIGNAL;
    END;

    
    START TRANSACTION;
     
     
     
     
     

       
       IF voucher_event_code = 'SPMSRF' THEN
      
       
    
        
        SELECT acc_coa_id INTO v_coa_SalesReturn
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 28 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SerciceReturn 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =33 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        

          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
                
                
                    
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        
        IF v_coa_SalesReturn IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
	    
	    SELECT IFNULL(sr.totalamount,0),IFNULL(sr.totaldiscount,0),IFNULL(sr.total_vat,0),IFNULL(sr.pay_amount,0),IFNULL(sr.service_charge,0) ,sr.createby, sr.return_date, sr.subcode_id, IFNULL(sr.sub_total,0)
	    INTO        v_sr_totalamount,        v_sr_totaldiscount,      v_sr_total_vat,          v_sr_pay_amount,        v_sr_service_charge,    v_create_by, v_return_date, v_subcode_id, v_sr_sub_total
	    FROM sale_return sr  
	    WHERE sr.oreturn_id =in_id AND sr.pay_status = 1 AND sr.voucher_event_code = 'SPMSRF' AND sr.VoucherNumber IS NULL LIMIT 1;



	    SELECT IFNULL(pay_amount,0), acc_coa_id
	    INTO v_pay_amount, v_coa_PaymentMethod 
	    FROM tbl_return_payment WHERE oreturn_id=in_id LIMIT 1;
 

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_return_date BETWEEN start_date AND end_date AND is_active = 1;
            
           IF v_financialYearId IS NOT NULL AND v_pay_amount IS NOT NULL  THEN
           
            

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), CONCAT(voucher_event_code,' POS Sales Return Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_SalesReturn, (v_sr_sub_total+v_sr_total_vat) , 0.00);
            
            IF v_sr_service_charge >0.00 THEN
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount )
            VALUES (voucher_master_id, v_coa_SerciceReturn, v_sr_service_charge , 0.00 );
            END IF;
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), 3, v_subcode_id);
            
            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
            IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


	IF v_sr_total_vat > 0.00 THEN
	   
	    SET voucher_type_id = 3;
	    INSERT INTO acc_voucher_master (
		VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
	    )
	    VALUES (
		GetNewVoucherNumber(voucher_type_id,v_return_date), 
		GetNewVoucherNumber(voucher_type_id,v_return_date), 
		v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sr_total_vat, CONCAT(voucher_event_code,' POS Vat Payable Sales Return Number : ', in_id), v_create_by, NOW(), in_id
	    );
	    SET voucher_master_id = LAST_INSERT_ID();

	    
	    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	    VALUES (voucher_master_id, v_coa_vat_payable, v_sr_total_vat,0.00);
	   
	    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	    VALUES (voucher_master_id, v_coa_SalesReturn ,0.00,v_sr_total_vat);

	    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
	     SET message = CONCAT(message, op_voucherNumber,',');
	
	    
	    IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
	 
	 END IF;
         
            
            
            SET voucher_type_id = 1;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), CONCAT(voucher_event_code,' POS Payment for Sales Return Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

         
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_sr_sub_total+v_sr_total_vat+v_sr_service_charge) , 0.00, 3, v_subcode_id);
           
            IF v_sr_totaldiscount > 0.00 THEN
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount,0.00, v_sr_totaldiscount );
            END IF;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, 0.00, v_pay_amount);
         
            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE sale_return SET VoucherNumber = message WHERE oreturn_id= in_id LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR SALES RETURN';
        END IF;   	
    ELSE
        SET message = 'SALES RETURN/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;  
     

     
     ELSEIF voucher_event_code = 'SPMSRP' THEN
    
      
        SELECT acc_coa_id INTO v_coa_SalesReturn
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 28 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
         
        SELECT acc_coa_id INTO v_coa_SerciceReturn 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =33 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        

          
        SELECT acc_coa_id INTO v_coa_vat_payable 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id =11 AND p.is_active = TRUE AND ps.is_active = TRUE;
                
                
                    
        SELECT acc_coa_id INTO v_coa_SalesDiscount 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 7 AND p.is_active = TRUE AND ps.is_active = TRUE;

        IF v_coa_SalesReturn IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
	    
	    SELECT IFNULL(sr.totalamount,0),IFNULL(sr.totaldiscount,0),IFNULL(sr.total_vat,0),IFNULL(sr.pay_amount,0),IFNULL(sr.service_charge,0) ,sr.createby, sr.return_date, sr.subcode_id, IFNULL(sr.sub_total,0)
	    INTO        v_sr_totalamount,        v_sr_totaldiscount,      v_sr_total_vat,          v_sr_pay_amount,        v_sr_service_charge,    v_create_by, v_return_date, v_subcode_id, v_sr_sub_total
	    FROM sale_return sr  
	    WHERE sr.oreturn_id =in_id AND sr.pay_status = 1 AND sr.voucher_event_code = 'SPMSRP' AND sr.VoucherNumber IS NULL LIMIT 1;



	    SELECT IFNULL(pay_amount,0), acc_coa_id
	    INTO v_pay_amount, v_coa_PaymentMethod 
	    FROM tbl_return_payment WHERE oreturn_id=in_id LIMIT 1;
 

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_return_date BETWEEN start_date AND end_date AND is_active = 1;
            
           IF v_financialYearId IS NOT NULL AND v_pay_amount IS NOT NULL  THEN
           
            

            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), CONCAT(voucher_event_code,' POS Sales Return Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_SalesReturn, (v_sr_sub_total+v_sr_total_vat) , 0.00);
            
            IF v_sr_service_charge >0.00 THEN
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount )
            VALUES (voucher_master_id, v_coa_SerciceReturn, v_sr_service_charge , 0.00 );
            END IF;
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), 3, v_subcode_id);
            
            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT( op_voucherNumber,','); 
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;


	IF v_sr_total_vat > 0.00 THEN
	   
	    SET voucher_type_id = 3;
	    INSERT INTO acc_voucher_master (
		VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
	    )
	    VALUES (
		GetNewVoucherNumber(voucher_type_id,v_return_date), 
		GetNewVoucherNumber(voucher_type_id,v_return_date), 
		v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sr_total_vat, CONCAT(voucher_event_code,' POS Vat Payable Sales Return Number : ', in_id), v_create_by, NOW(), in_id
	    );
	    SET voucher_master_id = LAST_INSERT_ID();

	    
	    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	    VALUES (voucher_master_id, v_coa_vat_payable, v_sr_total_vat,0.00);
	   
	    INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	    VALUES (voucher_master_id, v_coa_SalesReturn ,0.00,v_sr_total_vat);

	    SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
	     SET message = CONCAT(message, op_voucherNumber,',');
	
	    
	     IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
	 
	 END IF;
         
            
            
            SET voucher_type_id = 1;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, oreturn_id
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                v_return_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sr_sub_total+v_sr_total_vat+v_sr_service_charge), CONCAT(voucher_event_code,' POS Payment for Sales Return Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

         
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_sr_sub_total+v_sr_total_vat+v_sr_service_charge) , 0.00, 3, v_subcode_id);
           
            IF v_sr_totaldiscount > 0.00 THEN
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount,0.00, v_sr_totaldiscount );
            END IF;
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, 0.00, v_pay_amount);
         
            SET op_voucherNumber = (SELECT VoucherNumber FROM acc_voucher_master WHERE id = voucher_master_id LIMIT 1);
            SET message = CONCAT(message, op_voucherNumber);
            
             IF (SELECT approval_for_sales_voucher FROM setting LIMIT 1) = 1 THEN
			CALL AccVoucherApprove(voucher_master_id, @app_message);
		    END IF;
            
            UPDATE sale_return SET VoucherNumber = message WHERE oreturn_id= in_id LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR SALES RETURN';
        END IF;   	
    ELSE
        SET message = 'SALES RETURN/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;  























        ELSEIF voucher_event_code = 'SPMS-SRA' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMS-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(mb.amount,0), pm.acc_coa_id 
	    INTO v_pm_amount, v_coa_PaymentMethod 
	    FROM multipay_bill mb 
	    INNER JOIN payment_method pm ON mb.payment_method_id = pm.payment_method_id 
	    WHERE mb.bill_id = in_id LIMIT 1;

            SELECT fiyear_id INTO v_financialYearId
            FROM acc_financialyear 
            WHERE v_bill_date BETWEEN start_date AND end_date AND is_active = 1;

           IF v_financialYearId IS NOT NULL AND v_bill_amount > 0.00 AND v_return_amount >0.00 AND v_return_order_id > 0 THEN
            
            SET voucher_type_id = 3;
            INSERT INTO acc_voucher_master (
                VoucherNumber, VoucherNumberMainBreanch, VoucherDate, Companyid, BranchId, FinancialYearId, VoucharTypeId, Voucher_event_code, TranAmount, Remarks, Createdby, CreatedDate, BillID
            )
            VALUES (
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                GetNewVoucherNumber(voucher_type_id,v_return_date), 
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount-v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_bill_amount - v_return_amount, 0.00, 3, v_subcode_id);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_amount );

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
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_bill_amount -v_return_amount, 3, v_subcode_id);

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
    	
    
    	
        ELSEIF voucher_event_code = 'SPMSD-SRA' THEN 
            
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSD-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount-v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, v_sales_amount-v_return_amount, 0.00, 3, v_subcode_id);
            

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id,v_coa_SalesAcc, 0.00, v_sales_amount-v_return_amount);

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount-v_return_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount-v_return_amount, 3, v_subcode_id);

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
    
     ELSEIF voucher_event_code = 'SPMSV-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

	    SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id; 

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_sales_amount+v_vat_payable_amount)- v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,((v_sales_amount+v_vat_payable_amount)-v_return_amount), 0.00, 3, v_subcode_id);
            
           
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, (v_sales_amount-v_return_order_total_amount));

           
           
           INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
	   VALUES (voucher_master_id, v_coa_vat_payable ,0.00,(v_vat_payable_amount-v_return_order_vat_amount));
           
            
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
            VALUES (voucher_master_id, v_coa_Customer, 0.00, ((v_sales_amount+v_vat_payable_amount)-v_return_amount), 3, v_subcode_id);

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
    
    ELSEIF voucher_event_code = 'SPMSVI-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;
           
		   SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_sales_amount-v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_bill_amount-v_return_amount), 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, (v_sales_amount-v_return_order_total_amount));

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_vat_payable_amount-v_return_order_vat_amount), CONCAT(voucher_event_code,' POS Vat Payable Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, (v_vat_payable_amount-v_return_order_vat_amount),0.00);
           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,(v_vat_payable_amount-v_return_order_vat_amount));

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_bill_amount-v_return_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00,( v_bill_amount-v_return_amount), 3, v_subcode_id);

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
    
     
     ELSEIF voucher_event_code = 'SPMSS-SRA' THEN 
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount, v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSS-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_bill_amount-v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_bill_amount-v_return_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, (v_sales_amount-v_return_amount));

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_bill_amount-v_return_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_bill_amount-v_return_amount), 3, v_subcode_id);

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
    
     ELSEIF voucher_event_code = 'SPMSDV-SRA' THEN 
        
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
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSDV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;
            
            SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  
	   
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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_sales_amount+v_vat_payable_amount)-v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,((v_sales_amount+v_vat_payable_amount)-v_return_amount), 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);
            
                 
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount-v_return_order_vat_amount); 

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_sales_amount+v_vat_payable_amount)-v_return_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_sales_amount+v_vat_payable_amount)-v_return_order_vat_amount, 3, v_subcode_id);

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
    
    
       ELSEIF voucher_event_code = 'SPMSDVI-SRA' THEN 
        
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
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSDVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

            SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  
 
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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount - v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,v_sales_amount-v_return_amount, 0.00, 3, v_subcode_id);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);
            

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount-v_return_order_vat_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

	
        
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, v_vat_payable_amount-v_return_order_vat_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable ,0.00,v_vat_payable_amount-v_return_order_vat_amount); 

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_sales_amount-v_return_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount ,v_saled_discount_amount , 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, v_sales_amount-v_return_amount, 3, v_subcode_id);

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
        
    
    
     ELSEIF voucher_event_code = 'SPMSSV-SRA' THEN  
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

            SELECT IFNULL(VAT,0),IFNULL(total_amount,0), IFNULL(service_charge,0) INTO v_return_order_vat_amount,v_return_order_total_amount, v_return_service_amount FROM bill WHERE bill_id = v_return_order_id;  
 
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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,((v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount), 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount-v_return_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);

           
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount-v_return_order_vat_amount);




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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount), CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, ((v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount), 3, v_subcode_id);

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
    
    
    
     ELSEIF voucher_event_code = 'SPMSSVI-SRA' THEN  
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, ((v_service_amount+v_sales_amount)-v_return_amount), CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount)-v_return_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);

   

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount-v_return_order_vat_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

           

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount-v_return_order_vat_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount-v_return_order_vat_amount);

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount-v_return_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount)-v_return_amount, 3, v_subcode_id);

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
   
     ELSEIF voucher_event_code = 'SPMSSD-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSD-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount)-v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount)-v_return_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_amount);



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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_bill_amount-v_return_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount)-v_return_amount, 3, v_subcode_id);

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
    
         ELSEIF voucher_event_code = 'SPMSSDV-SRA' THEN 
        
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
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSDV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

 SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount-v_return_order_vat_amount);

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount , CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);


            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount+v_vat_payable_amount)-v_return_amount , 3, v_subcode_id);

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
    
    
    ELSEIF voucher_event_code = 'SPMSSDVI-SRA' THEN 
        
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
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id , IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'SPMSSDVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

 SELECT IFNULL(VAT,0),IFNULL(total_amount,0) INTO v_return_order_vat_amount,v_return_order_total_amount FROM bill WHERE bill_id = v_return_order_id;  
 
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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount)-v_return_amount, CONCAT(voucher_event_code,' POS Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer,(v_service_amount+v_sales_amount)-v_return_amount, 0.00, 3, v_subcode_id);
            
             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_ServiceIncome ,0.00,v_service_amount);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc, 0.00, v_sales_amount-v_return_order_total_amount);

           

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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, v_vat_payable_amount-v_return_order_vat_amount, CONCAT(voucher_event_code,' POS Vat Payable Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();



            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesAcc,v_vat_payable_amount-v_return_order_vat_amount, 0.00);

             
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_vat_payable, 0.00, v_vat_payable_amount-v_return_order_vat_amount);



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
                v_bill_date, 0, 0, v_financialYearId, voucher_type_id, voucher_event_code, (v_service_amount+v_sales_amount)-v_return_amount, CONCAT(voucher_event_code,' POS Received for Sales Order Number : ', in_id), v_create_by, NOW(), in_id
            );
            SET voucher_master_id = LAST_INSERT_ID();

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_PaymentMethod, v_pm_amount, 0.00);
            
            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount)
            VALUES (voucher_master_id, v_coa_SalesDiscount, v_saled_discount_amount, 0.00);

            
            INSERT INTO acc_voucher_details (voucher_master_id, acc_coa_id, Dr_Amount, Cr_Amount, subtype_id, subcode_id)
            VALUES (voucher_master_id, v_coa_Customer, 0.00, (v_service_amount+v_sales_amount)-v_return_amount , 3, v_subcode_id);

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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
   

    ELSEIF voucher_event_code = 'MPMS-SRA' THEN 
        
        SELECT acc_coa_id INTO v_coa_SalesAcc 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 6 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        
        SELECT acc_coa_id INTO v_coa_Customer 
        FROM acc_predefined p	
        INNER JOIN acc_predefined_seeting ps ON ps.predefined_id = p.id
        WHERE p.id = 8 AND p.is_active = TRUE AND ps.is_active = TRUE;
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN	
	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMS-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    	
    
    ELSEIF voucher_event_code = 'MPMSD-SRA' THEN 
            
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
            SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSD-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
     ELSEIF voucher_event_code = 'MPMSV-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    ELSEIF voucher_event_code = 'MPMSVI-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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

            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
     
     ELSEIF voucher_event_code = 'MPMSS-SRA' THEN 
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSS-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
     ELSEIF voucher_event_code = 'MPMSDV-SRA' THEN 
        
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
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSDV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
    
    
       ELSEIF voucher_event_code = 'MPMSDVI-SRA' THEN 
        
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
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	    SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSDVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully');  
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE';
    END IF;
        
    
    
     ELSEIF voucher_event_code = 'MPMSSV-SRA' THEN  
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
    
    
    
     ELSEIF voucher_event_code = 'MPMSSVI-SRA' THEN  
      
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
   
     ELSEIF voucher_event_code = 'MPMSSD-SRA' THEN 
        
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
        
        
        IF v_coa_SalesAcc IS NOT NULL AND v_coa_Customer IS NOT NULL THEN
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0) 
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSD-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
         ELSEIF voucher_event_code = 'MPMSSDV-SRA' THEN 
        
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
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSDV-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
        END IF;   	
    ELSE
        SET message = 'SALES/CUSTOMER ACCOUNT NOT SET IN PREDIFINE'; 
    END IF;
    
    
    ELSEIF voucher_event_code = 'MPMSSDVI-SRA' THEN 
        
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
        	    
	     SELECT IFNULL(b.bill_amount,0),IFNULL(b.total_amount,0) ,IFNULL(b.discount,0), IFNULL(b.VAT,0), b.bill_date, IFNULL(b.service_charge,0), IFNULL(b.deliverycharge,0), b.create_by, b.subcode_id, IFNULL(b.return_amount,0),IFNULL(b.return_order_id,0)
	    INTO v_bill_amount, v_sales_amount, v_saled_discount_amount, v_vat_payable_amount, v_bill_date, v_service_amount, v_deliverycharge_amount, v_create_by, v_subcode_id ,v_return_amount,v_return_order_id
	    FROM bill b  
	    WHERE b.bill_id =in_id AND b.bill_status = 1 AND b.voucher_event_code = 'MPMSSDVI-SRA' AND b.VoucherNumber IS NULL LIMIT 1;

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
            
            UPDATE bill SET VoucherNumber = message WHERE bill_id = in_id AND bill_status = 1 LIMIT 1;
            SET message = CONCAT(message, ' Voucher Created Successfully'); 
        ELSE
            SET message = 'VOUCHER NOT CREATED CHECK YOUR BILL';
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
        DB::unprepared("DROP PROCEDURE IF EXISTS AccIntegrationVoucherPosting_SalesReturn");
    }
};
