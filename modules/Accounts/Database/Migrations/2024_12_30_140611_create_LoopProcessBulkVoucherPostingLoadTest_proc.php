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
        DB::unprepared("CREATE PROCEDURE `LoopProcessBulkVoucherPostingLoadTest`()
BEGIN
    DECLARE counter INT DEFAULT 0;

    
    WHILE counter < 500 DO
        
        CALL ProcessBulkVoucherPostingLoadTest();
        
        
        SET counter = counter + 1;
    END WHILE;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS LoopProcessBulkVoucherPostingLoadTest");
    }
};
