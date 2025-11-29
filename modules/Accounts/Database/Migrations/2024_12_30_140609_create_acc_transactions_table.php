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
        Schema::create('acc_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('voucher_master_id')->index('voucher_master_id');
            $table->integer('Companyid')->nullable()->default(0)->index('Companyid');
            $table->unsignedBigInteger('BranchId')->nullable()->index('BranchId');
            $table->integer('FinancialYearId')->index('FinancialYearId');
            $table->integer('VoucharTypeId')->index('VoucharTypeId');
            $table->string('voucher_event_code', 25);
            $table->string('VoucherNumber', 255);
            $table->text('Remarks')->nullable();
            $table->date('VoucherDate');
            $table->unsignedBigInteger('acc_coa_id')->index('acc_coa_id');
            $table->unsignedInteger('subtype_id')->nullable()->index('subtype_id');
            $table->integer('subcode_id')->nullable()->index('subcode_id');
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->boolean('is_honour')->default(false);
            $table->tinyText('ledger_comment')->nullable();
            $table->decimal('Dr_Amount', 19, 3)->default(0);
            $table->decimal('Cr_Amount', 19, 3)->default(0);
            $table->unsignedBigInteger('reverse_acc_coa_id')->index('reverse_acc_coa_id');
            $table->unsignedBigInteger('PurchaseID')->nullable()->index('PurchaseID');
            $table->unsignedBigInteger('BillID')->nullable()->index('BillID');
            $table->integer('ServiceID')->nullable();
            $table->boolean('IsYearClosed')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->date('created_date');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->index(['BranchId'], 'fk_BranchId_acc_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_transactions');
    }
};
