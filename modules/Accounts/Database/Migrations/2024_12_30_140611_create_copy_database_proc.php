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
        DB::unprepared("CREATE PROCEDURE `copy_database`(IN `original_db` VARCHAR(64), IN `new_db` VARCHAR(64))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(64);
    DECLARE cur CURSOR FOR 
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = original_db;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    
    SET @create_db = CONCAT('CREATE DATABASE IF NOT EXISTS ', new_db);
    PREPARE stmt FROM @create_db;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        
        IF table_name IS NOT NULL THEN
            
            SET @create_stmt = CONCAT('CREATE TABLE ', new_db, '.', table_name, ' LIKE ', original_db, '.', table_name);
            SET @insert_stmt = CONCAT('INSERT INTO ', new_db, '.', table_name, ' SELECT * FROM ', original_db, '.', table_name);

            
            PREPARE stmt FROM @create_stmt;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;

            
            PREPARE stmt FROM @insert_stmt;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END LOOP;

    CLOSE cur;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS copy_database");
    }
};
