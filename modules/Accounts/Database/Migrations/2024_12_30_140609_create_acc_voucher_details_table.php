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
        Schema::create('acc_voucher_details', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('voucher_master_id')->index('voucher_master_id');
            $table->unsignedBigInteger('acc_coa_id')->index('acc_coa_id');
            $table->decimal('Dr_Amount', 19, 3)->default(0);
            $table->decimal('Cr_Amount', 19, 3)->default(0);
            $table->unsignedInteger('subtype_id')->nullable()->index('acc_voucher_details_subtype_id');
            $table->integer('subcode_id')->nullable()->index('acc_voucher_details_subcode_id');
            $table->text('LaserComments')->nullable();
            $table->string('chequeno', 50)->nullable();
            $table->date('chequeDate')->nullable();
            $table->boolean('ishonour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_voucher_details');
    }
};
