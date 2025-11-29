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
        Schema::create('acc_openingbalance', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('financial_year_id')->index('fiyear_id');
            $table->unsignedBigInteger('acc_coa_id')->index('coas_id');
            $table->text('account_code')->nullable();
            $table->decimal('debit', 19, 3)->default(0);
            $table->decimal('credit', 19, 3)->default(0);
            $table->date('open_date')->nullable()->default('1970-01-01');
            $table->unsignedInteger('acc_subtype_id')->nullable()->index('subtypeid');
            $table->integer('acc_subcode_id')->nullable()->index('subcode');
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->dateTime('created_at')->default('1970-01-01 01:01:01');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_openingbalance');
    }
};
