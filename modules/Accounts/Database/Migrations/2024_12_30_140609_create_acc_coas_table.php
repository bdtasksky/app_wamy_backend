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
        Schema::create('acc_coas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->string('account_code')->nullable();
            $table->string('account_name');
            $table->unsignedBigInteger('head_level');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('acc_type_id');
            $table->boolean('is_cash_nature')->default(false);
            $table->boolean('is_bank_nature')->default(false);
            $table->boolean('is_budget')->default(false);
            $table->boolean('is_depreciation')->default(false);
            $table->integer('depreciation_rate')->nullable();
            $table->boolean('is_subtype')->default(false);
            $table->unsignedInteger('subtype_id')->nullable()->index('fk_acc_coas_subtype_id');
            $table->boolean('is_stock')->default(false);
            $table->boolean('is_fixed_asset_schedule')->default(false);
            $table->string('note_no')->nullable();
            $table->string('asset_code')->nullable();
            $table->string('dep_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['acc_type_id', 'account_code', 'account_name', 'asset_code', 'created_at', 'created_by', 'deleted_at'], 'Account_Type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_coas');
    }
};
