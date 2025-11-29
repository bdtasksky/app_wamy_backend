<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_report_formate', function (Blueprint $table) {
            $table->foreign(['report_id'], 'acc_report_formate_report_id')->references(['id'])->on('acc_report_name')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_report_formate', function (Blueprint $table) {
            $table->dropForeign('acc_report_formate_report_id');
        });
    }
};
