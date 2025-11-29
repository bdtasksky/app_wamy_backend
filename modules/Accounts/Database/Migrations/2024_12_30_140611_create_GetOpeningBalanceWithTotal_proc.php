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
        DB::unprepared("CREATE PROCEDURE `GetOpeningBalanceWithTotal`(IN `financial_year` INT)
BEGIN
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
    INNER JOIN 
        acc_subcode sc ON sc.id = o.acc_subcode_id
    INNER JOIN 
        acc_subtype st ON st.id = o.acc_subtype_id
    WHERE 
        o.financial_year_id = financial_year
    UNION ALL
    SELECT 
        'Total',
        'Total',
        'Total',
        'Total',
        NULL,
        NULL,
        SUM(CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.debit 
        END),
        SUM(CASE 
            WHEN t.id = 2 OR t.id = 3 THEN 0 
            ELSE o.credit 
        END)
    FROM 
        acc_openingbalance o
    INNER JOIN 
        acc_coas a ON o.acc_coa_id = a.id
    INNER JOIN 
        acc_types t ON t.id = a.acc_type_id
    WHERE 
        o.financial_year_id = financial_year
    ORDER BY 
        account_type_name;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetOpeningBalanceWithTotal");
    }
};
