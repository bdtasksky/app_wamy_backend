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
        DB::unprepared("CREATE PROCEDURE `GetWalletBalanceReport`(
    IN wallet_user_id BIGINT,
    IN from_date DATE,
    IN to_date DATE
)
BEGIN
    DECLARE opening_balance DECIMAL(10,3) DEFAULT 0;
    DECLARE current_balance DECIMAL(10,3) DEFAULT 0;

    -- Temporary table to store the report data
    CREATE TEMPORARY TABLE WalletReport (
        `sl` INT AUTO_INCREMENT PRIMARY KEY,
        `date` DATE,
        `status` ENUM('Received', 'Payment', 'Opening Balance', 'Current Balance'),
        `narration` VARCHAR(500),
        `expense_type` VARCHAR(100),
        `receive_amount` DECIMAL(10,3),
        `payment_amount` DECIMAL(10,3),
        `balance` DECIMAL(10,3)
    );

    -- Calculate the opening balance up to the day before from_date
    SELECT 
        IFNULL(SUM(CASE 
            WHEN transaction_status = 'Received' THEN amount 
            WHEN transaction_status = 'Payment' THEN -amount 
        END), 0)
    INTO opening_balance
    FROM wallet_users_transactions
    WHERE (from_wallet_users_id = wallet_user_id)
      AND posting_date < from_date;

    -- Insert the opening balance row
    INSERT INTO WalletReport (`date`, `status`, `narration`, `expense_type`, `receive_amount`, `payment_amount`, `balance`)
    VALUES (from_date, 'Opening Balance', NULL, NULL, NULL, NULL, opening_balance);

    SET current_balance = opening_balance;

    -- Insert transactions within the date range
    INSERT INTO WalletReport (`date`, `status`, `narration`, `expense_type`, `receive_amount`, `payment_amount`, `balance`)
    SELECT 
        t.posting_date AS `date`,
        t.transaction_status AS `status`,
        CASE
            WHEN t.transaction_status = 'Received' THEN CONCAT('Receive for ', wu.wallet_user_name)
            WHEN t.transaction_status = 'Payment' THEN 
                CASE 
                    WHEN t.expenses_id IS NOT NULL THEN 'Expense'
                    ELSE CONCAT('Payment to ', wu.wallet_user_name)
                END
            ELSE NULL
        END AS `narration`,
        (SELECT expense_type FROM expense_types WHERE id = t.expenses_id) AS `expense_type`,
        CASE WHEN t.transaction_status = 'Received' THEN t.amount ELSE NULL END AS `receive_amount`,
        CASE WHEN t.transaction_status = 'Payment' THEN t.amount ELSE NULL END AS `payment_amount`,
        @balance := @balance + CASE 
            WHEN t.transaction_status = 'Received' THEN t.amount 
            WHEN t.transaction_status = 'Payment' THEN -t.amount 
        END AS `balance`
        
    FROM wallet_users_transactions t
    LEFT JOIN wallet_users wu 
        ON t.to_wallet_users_id = wu.id,
    (SELECT @balance := opening_balance) AS vars
    WHERE (t.from_wallet_users_id = wallet_user_id)
      AND t.posting_date BETWEEN from_date AND to_date
    ORDER BY t.posting_date;


    -- Recalculate the current balance from all rows to ensure accuracy
    SELECT balance INTO current_balance
    FROM WalletReport
    WHERE balance IS NOT NULL
    ORDER BY sl DESC
    LIMIT 1;

    -- Insert the Current balance row
    INSERT INTO WalletReport (`date`, `status`, `narration`, `expense_type`, `receive_amount`, `payment_amount`, `balance`)
    VALUES (to_date, 'Current Balance', NULL, NULL, NULL, NULL, current_balance);

    -- Return the report
    SELECT * FROM WalletReport;

    -- Drop the temporary table
    DROP TEMPORARY TABLE WalletReport;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetWalletBalanceReport");
    }
};
