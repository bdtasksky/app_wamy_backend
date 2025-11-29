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
        Schema::table('acc_predefined_seeting', function (Blueprint $table) {
            $table->foreign(['acc_coa_id'], 'fk_acc_coa_id_acc_predefined_seeting')->references(['id'])->on('acc_coas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['predefined_id'], 'fk_predefined_id_acc_predefined_seeting')->references(['id'])->on('acc_predefined')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_predefined_seeting', function (Blueprint $table) {
            $table->dropForeign('fk_acc_coa_id_acc_predefined_seeting');
            $table->dropForeign('fk_predefined_id_acc_predefined_seeting');
        });
    }
};
