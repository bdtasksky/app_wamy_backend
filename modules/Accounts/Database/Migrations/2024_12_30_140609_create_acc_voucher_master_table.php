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
        Schema::create('acc_voucher_master', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('VoucherNumber', 255);
            $table->string('VoucherNumberMainBreanch', 255)->nullable();
            $table->date('VoucherDate');
            $table->integer('Companyid')->nullable()->default(0)->index('Companyid');
            $table->unsignedBigInteger('BranchId')->nullable()->index('BranchId');
            $table->integer('FinancialYearId')->index('FinancialYearId');
            $table->integer('VoucharTypeId')->index('VoucharTypeId');
            $table->string('Voucher_event_code', 25)->index('Voucher_event_code');
            $table->decimal('TranAmount', 10);
            $table->text('Remarks')->nullable();
            $table->string('Createdby', 100);
            $table->dateTime('CreatedDate')->useCurrent();
            $table->string('UpdatedBy', 100)->nullable();
            $table->dateTime('UpdatedDate')->default('1970-01-01 00:00:00');
            $table->boolean('IsApprove')->default(false);
            $table->string('Approvedby', 100)->nullable();
            $table->dateTime('Approvedate')->default('1970-01-01 00:00:00');
            $table->unsignedBigInteger('PurchaseID')->nullable()->index('PurchaseID');
            $table->unsignedBigInteger('BillID')->nullable()->index('BillID');
            $table->integer('ServiceID')->nullable();
            $table->boolean('IsYearClosed')->default(false);
            $table->unsignedBigInteger('preturn_id')->nullable()->index('fk_preturn_id_purchase_return');
            $table->unsignedBigInteger('oreturn_id')->nullable()->index('fk_oreturn_id_sale_return');

            $table->index(['BranchId'], 'fk_BranchId_acc_voucher_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_voucher_master');
    }
};
