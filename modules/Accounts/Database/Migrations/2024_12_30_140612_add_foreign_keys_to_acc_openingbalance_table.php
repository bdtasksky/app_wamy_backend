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
        Schema::table('acc_openingbalance', function (Blueprint $table) {
            $table->foreign(['acc_coa_id'], 'fk_coas_id')->references(['id'])->on('acc_coas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['financial_year_id'], 'fk_fiyear_id')->references(['fiyear_id'])->on('acc_financialyear')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['acc_subtype_id'], 'fk_subtypeid')->references(['id'])->on('acc_subtype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_openingbalance', function (Blueprint $table) {
            $table->dropForeign('fk_coas_id');
            $table->dropForeign('fk_fiyear_id');
            $table->dropForeign('fk_subtypeid');
        });
    }
};
