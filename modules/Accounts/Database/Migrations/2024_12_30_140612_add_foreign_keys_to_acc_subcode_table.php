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
        Schema::table('acc_subcode', function (Blueprint $table) {
            $table->foreign(['subTypeID'], 'fk_subcode_subtype')->references(['id'])->on('acc_subtype')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acc_subcode', function (Blueprint $table) {
            $table->dropForeign('fk_subcode_subtype');
        });
    }
};
