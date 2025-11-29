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
        Schema::table('acc_voucher_details', function (Blueprint $table) {
            $table->foreign(['acc_coa_id'], 'fk_acc_coa_id_acc_coas')->references(['id'])->on('acc_coas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['subtype_id'], 'fk_subtypeid_acc_voucher_details')->references(['id'])->on('acc_subtype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['voucher_master_id'], 'fk_voucher_master_id')->references(['id'])->on('acc_voucher_master')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_voucher_details', function (Blueprint $table) {
            $table->dropForeign('fk_acc_coa_id_acc_coas');
            $table->dropForeign('fk_subtypeid_acc_voucher_details');
            $table->dropForeign('fk_voucher_master_id');
        });
    }
};
