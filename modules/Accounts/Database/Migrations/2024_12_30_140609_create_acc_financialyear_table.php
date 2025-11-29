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
        Schema::create('acc_financialyear', function (Blueprint $table) {
            $table->integer('fiyear_id', true);
            $table->string('title', 50);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->dateTime('date_time')->nullable();
            $table->string('is_active', 3)->nullable()->comment('0=inactive,1=active,2=ended');
            $table->string('create_by', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_financialyear');
    }
};
