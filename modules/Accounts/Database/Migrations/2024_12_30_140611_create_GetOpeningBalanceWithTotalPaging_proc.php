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
        DB::unprepared("CREATE PROCEDURE `GetOpeningBalanceWithTotalPaging`(IN `financial_year` INT, IN `p_limit` INT, IN `p_page_number` INT, OUT `op_total_row` INT)
BEGIN
    
    DECLARE p_offset INT;

    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_table_opening_balance (
        title VARCHAR(255),
        start_date VARCHAR(255),
        account_type_name VARCHAR(255),
        account_name VARCHAR(255),
        subtype_name VARCHAR(255),
        subcode_name VARCHAR(255),
        debit DECIMAL(15,2),
        credit DECIMAL(15,2)
    );

    
    INSERT INTO temp_table_opening_balance (
        title, start_date, account_type_name, account_name, 
        subtype_name, subcode_name, debit, credit
    )
    SELECT 
        f.title,
        f.start_date,
        t.account_type_name,
        a.account_name,
        st.name AS subtype_name,
        sc.name AS subcode_name,
        CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.debit 
        END AS debit,
        CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.credit 
        END AS credit
    FROM 
        acc_openingbalance o
    INNER JOIN 
        acc_coas a ON o.acc_coa_id = a.id
    INNER JOIN 
        acc_types t ON t.id = a.acc_type_id
    INNER JOIN 
        acc_financialyear f ON f.fiyear_id = o.financial_year_id
    LEFT JOIN 
        acc_subcode sc ON sc.id = o.acc_subcode_id
    LEFT JOIN 
        acc_subtype st ON st.id = o.acc_subtype_id
    WHERE 
        o.financial_year_id = financial_year

    UNION ALL

    SELECT 
        '' AS title,
        '' AS start_date,
        'Total' AS account_type_name,
        'Total' AS account_name,
        NULL AS subtype_name,
        NULL AS subcode_name,
        SUM(CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.debit 
        END) AS debit,
        SUM(CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.credit 
        END) AS credit
    FROM 
        acc_openingbalance o
    INNER JOIN 
        acc_coas a ON o.acc_coa_id = a.id
    INNER JOIN 
        acc_types t ON t.id = a.acc_type_id
    WHERE 
        o.financial_year_id = financial_year;

    
    SELECT COUNT(*) INTO op_total_row FROM temp_table_opening_balance;

    
    SET p_offset = IFNULL(((p_page_number - 1) * p_limit), 0);

    
    IF p_limit = -1 THEN
        SELECT * FROM temp_table_opening_balance; 
    ELSE
        SELECT * FROM temp_table_opening_balance LIMIT p_limit OFFSET p_offset; 
    END IF;

    
    DROP TEMPORARY TABLE IF EXISTS temp_table_opening_balance;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetOpeningBalanceWithTotalPaging");
    }
};
