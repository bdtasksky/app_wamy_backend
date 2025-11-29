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
        Schema::table('acc_transactions', function (Blueprint $table) {
            $table->foreign(['acc_coa_id'], 'fk_acc_coa_id_acc_coas_transactions')->references(['id'])->on('acc_coas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['BillID'], 'fk_BillID_acc_transactions')->references(['id'])->on('branch_sales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['BranchId'], 'fk_BranchId_acc_transactions_branches')->references(['id'])->on('branches')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['FinancialYearId'], 'fk_financial_year_id_acc_transactions')->references(['fiyear_id'])->on('acc_financialyear')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['PurchaseID'], 'fk_PurchaseID_acc_transactions')->references(['id'])->on('purchases')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['reverse_acc_coa_id'], 'fk_reverse_acc_coa_id_acc_coas_transactions')->references(['id'])->on('acc_coas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['subtype_id'], 'fk_subtypeid_acc_transactions')->references(['id'])->on('acc_subtype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['VoucharTypeId'], 'fk_VoucharTypeId_acc_transactions')->references(['id'])->on('acc_vouchartype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['voucher_master_id'], 'fk_voucher_master_id_acc_transactions')->references(['id'])->on('acc_voucher_master')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_transactions', function (Blueprint $table) {
            $table->dropForeign('fk_acc_coa_id_acc_coas_transactions');
            $table->dropForeign('fk_BillID_acc_transactions');
            $table->dropForeign('fk_BranchId_acc_transactions_branches');
            $table->dropForeign('fk_financial_year_id_acc_transactions');
            $table->dropForeign('fk_PurchaseID_acc_transactions');
            $table->dropForeign('fk_reverse_acc_coa_id_acc_coas_transactions');
            $table->dropForeign('fk_subtypeid_acc_transactions');
            $table->dropForeign('fk_VoucharTypeId_acc_transactions');
            $table->dropForeign('fk_voucher_master_id_acc_transactions');
        });
    }
};
