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
        DB::unprepared("CREATE PROCEDURE `InsertProduct`(IN `jsonData` JSON)
BEGIN
DECLARE opening_amount DOUBLE(15,2);
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        -- Rollback the transaction if an error occurs
        ROLLBACK;
    END;
    -- Start the transaction
    START TRANSACTION;
    

DROP TABLE IF EXISTS test_json_temp;
CREATE TABLE test_json_temp (
ProductCode	VARCHAR(500),
Brand	VARCHAR(500),
Category VARCHAR(500),
SubCategory	VARCHAR(500),
Supplier	VARCHAR(500),
ProductName	VARCHAR(500),
BarcodeQrcode	VARCHAR(500),
SalePrice	VARCHAR(500),
CostPrice	VARCHAR(500),
Unit	VARCHAR(500),
CurrentStock VARCHAR(500),	
WarehouseName	VARCHAR(500),
ProductModel	VARCHAR(500),
ProductDetails VARCHAR(500),
IsSaleable VARCHAR(500),
IsServiceable VARCHAR(500),
IsSerialNumber VARCHAR(500),
IsWarranty VARCHAR(500),
warranty VARCHAR(500)
)DEFAULT COLLATE=utf8mb4_unicode_ci;

   
   
    DROP TEMPORARY TABLE IF EXISTS temp_table;
    -- Create a derived table from JSON data
    CREATE TEMPORARY TABLE temp_table AS
    SELECT *
    FROM JSON_TABLE(
        jsonData, 
        \"$[*]\" COLUMNS (
            ProductCode VARCHAR(500) PATH \"$.ProductCode\",
            Brand VARCHAR(500) PATH \"$.Brand\",
            Category VARCHAR(500) PATH \"$.Category\",
            SubCategory VARCHAR(500) PATH \"$.SubCategory\",
            Supplier VARCHAR(500) PATH \"$.Supplier\",
            ProductName VARCHAR(500) PATH \"$.ProductName\",
            BarcodeQrcode VARCHAR(500) PATH \"$.BarcodeQrcode\",
            SalePrice VARCHAR(500) PATH \"$.SalePrice\",
            CostPrice VARCHAR(500) PATH \"$.CostPrice\",
            Unit VARCHAR(500) PATH \"$.Unit\",
            CurrentStock VARCHAR(500) PATH \"$.CurrentStock\",
            WarehouseName VARCHAR(500) PATH \"$.WarehouseName\",
            ProductModel VARCHAR(500) PATH \"$.ProductModel\",
            ProductDetails VARCHAR(500) PATH \"$.ProductDetails\",
            IsSaleable VARCHAR(500) PATH \"$.IsSaleable\",
            IsServiceable VARCHAR(500) PATH \"$.IsServiceable\",
            IsSerialNumber VARCHAR(500) PATH \"$.IsSerialNumber\",
            IsWarranty VARCHAR(500) PATH \"$.IsWarrantyMonth\"
            
        )
    ) AS jt;

 -- Insert data from the temporary table into the target table
    INSERT INTO test_json_temp (
        ProductCode, Brand, Category, SubCategory, Supplier, ProductName, 
        BarcodeQrcode, SalePrice, CostPrice, Unit, CurrentStock, 
        WarehouseName, ProductModel, ProductDetails, 
        IsSaleable,IsServiceable,IsSerialNumber,IsWarranty,warranty
    )
    SELECT 
        ProductCode, Brand, Category, SubCategory, Supplier, ProductName, 
        BarcodeQrcode, SalePrice, CostPrice, Unit, CurrentStock, 
        WarehouseName, ProductModel, ProductDetails,
        CASE WHEN IsSaleable = 'YES' THEN 1  ELSE 0 END AS is_saleable,
        CASE WHEN IsServiceable = 'YES' THEN 1  ELSE 0 END AS is_serviceable,
        CASE WHEN IsSerialNumber = 'YES' THEN 1  ELSE 0 END AS is_serialNumber,
        CASE WHEN IsWarranty > 0 THEN 1 ELSE 0 END AS is_warranty,
        CASE WHEN IsWarranty > 0 THEN IsWarranty ELSE 0 END AS warranty
    FROM temp_table;

    -- Clean up
    DROP TEMPORARY TABLE IF EXISTS temp_table;
-- brand..
INSERT INTO brands (`brand_name`, `status`, `created_at`)
SELECT DISTINCT Brand, 1, NOW()
FROM test_json_temp tjt
WHERE Brand IS NOT NULL
AND Brand != ''
AND NOT EXISTS (
    SELECT 1
    FROM brands b
    WHERE b.brand_name COLLATE utf8mb4_unicode_ci = tjt.Brand COLLATE utf8mb4_unicode_ci
);

-- categories
INSERT INTO categories (`category_name`, `status`, `created_at`)
SELECT DISTINCT Category, 1, NOW()
FROM test_json_temp tjt
WHERE Category IS NOT NULL
AND Category != ''
AND NOT EXISTS (
    SELECT 1
    FROM categories c
    WHERE c.category_name COLLATE utf8mb4_unicode_ci = tjt.Category COLLATE utf8mb4_unicode_ci
);

-- suppliers
INSERT INTO suppliers (`supplier_name`, `status`, `created_at`,supplier_type)
SELECT DISTINCT Supplier, 1, NOW(),'supplier'
FROM test_json_temp tjt
WHERE Supplier IS NOT NULL
AND Supplier != ''
AND NOT EXISTS (
    SELECT 1
    FROM suppliers s
    WHERE s.supplier_name COLLATE utf8mb4_unicode_ci = tjt.Supplier COLLATE utf8mb4_unicode_ci
);


-- warehouses
INSERT INTO warehouses (`name`, `status`, `created_at`)
SELECT DISTINCT WarehouseName, 1, NOW()
FROM test_json_temp tjt
WHERE WarehouseName IS NOT NULL
AND WarehouseName != ''
AND NOT EXISTS (
    SELECT 1
    FROM warehouses w
    WHERE w.name COLLATE utf8mb4_unicode_ci = tjt.WarehouseName COLLATE utf8mb4_unicode_ci
);

-- unit_of_measurements
INSERT INTO unit_of_measurements (`unit_name`, `status`, `created_at`)
SELECT DISTINCT Unit, 1, NOW()
FROM test_json_temp tjt
WHERE Unit IS NOT NULL
AND Unit != ''
AND NOT EXISTS (
    SELECT 1
    FROM unit_of_measurements u
    WHERE u.unit_name COLLATE utf8mb4_unicode_ci = tjt.Unit COLLATE utf8mb4_unicode_ci
);

-- SELECT * FROM sub_categories;
INSERT INTO sub_categories (`sub_category_name`, `status`, `created_at`, `category_id`)
SELECT DISTINCT 
    SubCategory, 
    1, 
    NOW(),
    (SELECT id FROM categories WHERE category_name COLLATE utf8mb4_unicode_ci = tjt.Category COLLATE utf8mb4_unicode_ci LIMIT 1)
FROM test_json_temp tjt
WHERE SubCategory IS NOT NULL
AND SubCategory != ''
AND NOT EXISTS (
    SELECT 1
    FROM sub_categories u
    WHERE u.sub_category_name COLLATE utf8mb4_unicode_ci = tjt.SubCategory COLLATE utf8mb4_unicode_ci
);


-- acc_subcodes

INSERT INTO acc_subcodes ( `name`, `acc_subtype_id`, `status`, `created_at`, reference_no)
SELECT DISTINCT Supplier, 3, 1,
    NOW(), 
    (SELECT id FROM suppliers WHERE `supplier_name` = tjt.Supplier LIMIT 1) AS reference
FROM test_json_temp tjt
WHERE Supplier IS NOT NULL
    AND Supplier != ''
    AND NOT EXISTS (
        SELECT 1
        FROM acc_subcodes s
        WHERE s.name COLLATE utf8mb4_unicode_ci = tjt.Supplier COLLATE utf8mb4_unicode_ci 
        AND s.acc_subtype_id = 3
    );



SELECT 
SUM(IFNULL(tjt.CostPrice, 0) * IFNULL(tjt.CurrentStock, 0)) INTO opening_amount
FROM test_json_temp tjt
WHERE tjt.ProductName IS NOT NULL
AND tjt.ProductName != ''
AND NOT EXISTS (
    SELECT 1
    FROM product_information p
    WHERE p.product_name COLLATE utf8mb4_unicode_ci = tjt.ProductName COLLATE utf8mb4_unicode_ci
);


-- Insert into product_information
INSERT INTO `product_information` (
    `product_code`, `brand_id`, `category_id`, `sub_category_id`, `supplier_id`, `unit`,`UOM_id`,
    `product_name`, `barcode_qrcode`, `price`, `cost`, `current_stock`,
    `product_model`, `product_details`,`is_multi`,`current_balance`,
    `is_saleable`,`is_warranty`,warranty,`is_serviceable`,`serial_number`
)
SELECT 
    ProductCode, 
    (SELECT id FROM brands WHERE brand_name = tjt.Brand LIMIT 1),
    (SELECT id FROM categories WHERE category_name = tjt.Category LIMIT 1),
    (SELECT id FROM sub_categories WHERE sub_category_name = tjt.SubCategory LIMIT 1),
    (SELECT id FROM suppliers WHERE supplier_name = tjt.Supplier LIMIT 1),
    (SELECT unit_name FROM unit_of_measurements WHERE unit_name = tjt.Unit LIMIT 1),
    (SELECT id FROM unit_of_measurements WHERE unit_name = tjt.Unit LIMIT 1),
    ProductName, 
    BarcodeQrcode, 
    SalePrice, 
    CostPrice, 
    CurrentStock, 
    ProductModel, 
    ProductDetails,
    0,
    IFNULL((IFNULL(tjt.CostPrice, 0) * IFNULL(tjt.CurrentStock, 0)),0), 
   IsSaleable, 
   IsWarranty,
   warranty, 
   IsServiceable,
   IsSerialNumber
   
   
   
FROM test_json_temp tjt
WHERE ProductName IS NOT NULL
AND ProductName != ''
AND NOT EXISTS (
    SELECT 1
    FROM product_information p
    WHERE p.product_name COLLATE utf8mb4_unicode_ci = tjt.ProductName COLLATE utf8mb4_unicode_ci
);


-- Inventory Opening Stock

INSERT INTO opening_stocks(product_id, `barcode`, warehouse_id, quantity, rate, total_price, opening_stock_date, added_by)
SELECT 
    p.id,
    p.barcode_qrcode,
    (SELECT id FROM warehouses WHERE `name` = j.WarehouseName LIMIT 1) AS warehouse_id, 
    p.current_stock,
    p.cost,
    (p.current_stock * p.cost) AS total_price,
    (SELECT start_date FROM financial_years WHERE `status` = 1 LIMIT 1) AS opening_stock_date,
    1 -- assuming 'added_by' is a static value
FROM 
    product_information p
INNER JOIN test_json_temp j ON j.ProductName = p.product_name
LEFT JOIN opening_stocks o ON o.product_id = p.id
WHERE o.product_id IS NULL;  -- Avoids inserting if already exists in opening_stocks



-- Accounting Opening balances
-- inventory_code
IF EXISTS (
    SELECT 1 
    FROM acc_opening_balances 
    WHERE acc_coa_id = (SELECT inventory_code FROM acc_predefine_accounts LIMIT 1) 
    AND financial_year_id = (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1)
)
THEN
    -- Update debit for inventory_code
    UPDATE acc_opening_balances 
    SET debit = debit + IFNULL (opening_amount,0)
    WHERE acc_coa_id = (SELECT inventory_code FROM acc_predefine_accounts LIMIT 1) 
    AND financial_year_id = (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1);

ELSE
    -- Insert debit for inventory_code
    INSERT INTO acc_opening_balances (financial_year_id, acc_coa_id, debit, open_date, created_at)
    VALUES (
        (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1),
        (SELECT inventory_code FROM acc_predefine_accounts),
         IFNULL (opening_amount,0), -- DR
        (SELECT start_date FROM financial_years WHERE `status` = 1 LIMIT 1),
        NOW()
    );
END IF;


-- capital_fund  
IF EXISTS (
    SELECT 1 
    FROM acc_opening_balances 
    WHERE acc_coa_id = (SELECT capital_fund FROM acc_predefine_accounts LIMIT 1) 
    AND financial_year_id = (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1)
)
THEN
    -- Update credit for capital_fund
    UPDATE acc_opening_balances 
    SET credit = credit + IFNULL (opening_amount,0)
    WHERE acc_coa_id = (SELECT capital_fund FROM acc_predefine_accounts LIMIT 1) 
    AND financial_year_id = (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1);

ELSE
    -- Insert credit for capital_fund
    INSERT INTO acc_opening_balances (financial_year_id, acc_coa_id, credit, open_date, created_at)
    VALUES (
        (SELECT id FROM financial_years WHERE `status` = 1 LIMIT 1),
        (SELECT capital_fund FROM acc_predefine_accounts),
        IFNULL (opening_amount,0), -- CR
        (SELECT start_date FROM financial_years WHERE `status` = 1 LIMIT 1),
        NOW()
    );
END IF;

COMMIT;
    -- Clean up
  DROP TABLE IF EXISTS test_json_temp;
  DROP TEMPORARY TABLE IF EXISTS temp_table;  
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS InsertProduct");
    }
};
