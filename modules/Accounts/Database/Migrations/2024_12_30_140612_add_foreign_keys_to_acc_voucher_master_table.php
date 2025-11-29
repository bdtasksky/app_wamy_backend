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
        Schema::table('acc_voucher_master', function (Blueprint $table) {
            $table->foreign(['BillID'], 'fk_BillID_branch_sales')->references(['id'])->on('branch_sales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['BranchId'], 'fk_BranchId_acc_voucher_master_branches')->references(['id'])->on('branches')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['FinancialYearId'], 'fk_financial_year_id')->references(['fiyear_id'])->on('acc_financialyear')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['oreturn_id'], 'fk_oreturn_id_sale_returns')->references(['id'])->on('sale_returns')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['preturn_id'], 'fk_preturn_id_purchase_returns')->references(['id'])->on('purchase_returns')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['PurchaseID'], 'fk_PurchaseID_purchases')->references(['id'])->on('purchases')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['VoucharTypeId'], 'fk_VoucharTypeId_acc_vouchartype')->references(['id'])->on('acc_vouchartype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_voucher_master', function (Blueprint $table) {
            $table->dropForeign('fk_BillID_branch_sales');
            $table->dropForeign('fk_BranchId_acc_voucher_master_branches');
            $table->dropForeign('fk_financial_year_id');
            $table->dropForeign('fk_oreturn_id_sale_returns');
            $table->dropForeign('fk_preturn_id_purchase_returns');
            $table->dropForeign('fk_PurchaseID_purchases');
            $table->dropForeign('fk_VoucharTypeId_acc_vouchartype');
        });
    }
};
