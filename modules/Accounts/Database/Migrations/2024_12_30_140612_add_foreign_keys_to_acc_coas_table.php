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
        Schema::table('acc_coas', function (Blueprint $table) {
            $table->foreign(['subtype_id'], 'fk_acc_coas_acc_subtype_id')->references(['id'])->on('acc_subtype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['acc_type_id'], 'fk_acc_coas_acc_type')->references(['id'])->on('acc_types')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_coas', function (Blueprint $table) {
            $table->dropForeign('fk_acc_coas_acc_subtype_id');
            $table->dropForeign('fk_acc_coas_acc_type');
        });
    }
};
