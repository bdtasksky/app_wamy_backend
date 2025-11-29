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
        Schema::create('acc_report_formate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('report_id')->index('acc_report_formate_report_id');
            $table->integer('srl');
            $table->enum('r_sign', ['=', '+', '-', '']);
            $table->string('r_space', 100);
            $table->integer('p_srl_row_val_1')->nullable();
            $table->enum('p_colum_amount_1', ['A', 'B', 'C'])->nullable();
            $table->enum('p_sign', ['+', '-'])->nullable();
            $table->integer('p_srl_row_val_2')->nullable();
            $table->enum('p_colum_amount_2', ['A', 'B', 'C'])->nullable();
            $table->integer('coa_acc_id')->nullable();
            $table->integer('sub_coa_acc_id')->nullable();
            $table->string('account_name', 255)->nullable();
            $table->enum('value_colum', ['', 'A', 'B', 'C'])->nullable();
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_report_formate');
    }
};
