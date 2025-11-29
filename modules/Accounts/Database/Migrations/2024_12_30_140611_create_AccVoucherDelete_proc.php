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
        DB::unprepared("CREATE DEFINER=`remote`@`%` PROCEDURE `AccVoucherDelete`(IN `p_pur_id` INT, IN `p_voucher_event_code` VARCHAR(25), OUT `message` VARCHAR(255))
BEGIN
    
    IF p_voucher_event_code = 'ACC' THEN
        IF NOT EXISTS (SELECT * FROM acc_transactions WHERE voucher_master_id = p_pur_id) THEN
            DELETE FROM acc_voucher_details WHERE voucher_master_id = p_pur_id;
            DELETE FROM acc_voucher_master WHERE ID = p_pur_id;
            SET message = 'Voucher delete success'; 
        ELSE  
            SET message = 'Approved voucher cannot be deleted'; 
        END IF;

    
    ELSEIF p_voucher_event_code LIKE '%PMP%' THEN
    
        IF EXISTS (SELECT * FROM purchaseitem WHERE purID = p_pur_id AND voucher_event_code = p_voucher_event_code) THEN
            IF EXISTS (SELECT * FROM acc_voucher_master WHERE PurchaseID = p_pur_id) THEN
               
                
                DELETE FROM acc_transactions 
                WHERE voucher_master_id IN (SELECT id FROM acc_voucher_master WHERE PurchaseID = p_pur_id);
                
                DELETE FROM acc_voucher_details 
                WHERE voucher_master_id IN (SELECT id FROM acc_voucher_master WHERE PurchaseID = p_pur_id);
                
                DELETE FROM acc_voucher_master WHERE PurchaseID = p_pur_id;
                
                
                DELETE FROM purchase_details WHERE purchaseid = p_pur_id; 
                DELETE FROM purchaseitem WHERE purID = p_pur_id AND voucher_event_code = p_voucher_event_code;
               
                
                SET message = 'Purchases record and accounting voucher deleted successfully'; 
            ELSE  
             
                DELETE FROM purchase_details WHERE purchaseid = p_pur_id; 
                DELETE FROM purchaseitem WHERE purID = p_pur_id AND voucher_event_code = p_voucher_event_code;
                 SET message = 'Purchases record deleted successfully'; 
            END IF;
        ELSE
            SET message = 'No purchase record found';   
        END IF;
    
    
    ELSEIF p_voucher_event_code LIKE '%PMPR%' THEN
        SET message = 'No Voucher Found'; 
    ELSEIF p_voucher_event_code LIKE '%PMS%' THEN
    
    IF EXISTS (SELECT * FROM bill WHERE order_id = p_pur_id AND voucher_event_code = p_voucher_event_code) THEN
            IF EXISTS (SELECT * FROM acc_voucher_master WHERE BillID = p_pur_id) THEN
               
                
                DELETE FROM acc_transactions 
                WHERE voucher_master_id IN (SELECT id FROM acc_voucher_master WHERE BillID = p_pur_id);
                
                DELETE FROM acc_voucher_details 
                WHERE voucher_master_id IN (SELECT id FROM acc_voucher_master WHERE BillID = p_pur_id);
                
                DELETE FROM acc_voucher_master WHERE BillID = p_pur_id;
                

               UPDATE customer_order  SET  isdelete=1 WHERE  order_id=p_pur_id;
               UPDATE bill SET VoucherNumber = NULL WHERE order_id=p_pur_id AND voucher_event_code = p_voucher_event_code;
               
                SET message = 'Sales Order record and accounting voucher deleted successfully'; 
            ELSE  
             
                 UPDATE customer_order  SET  isdelete=1 WHERE  order_id=p_pur_id;
                 UPDATE bill SET VoucherNumber = NULL WHERE order_id=p_pur_id AND voucher_event_code = p_voucher_event_code;
                 SET message = 'Sales Order record deleted successfully'; 
            END IF;
        ELSE
            SET message = 'No sales record found';   
        END IF;
    ELSEIF p_voucher_event_code LIKE '%PMSR%' THEN
        SET message = 'No Voucher Found'; 
    ELSE
        SET message = 'No Voucher Found'; 
    END IF;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS AccVoucherDelete");
    }
};
