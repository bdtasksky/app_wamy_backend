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
        Schema::create('acc_predefined_seeting', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('predefined_seeting_name', 150);
            $table->string('predefined_seeting_description', 250)->nullable();
            $table->unsignedInteger('predefined_id')->index('predefined_id');
            $table->unsignedBigInteger('acc_coa_id')->index('acc_coa_id');
            $table->boolean('is_active')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->timestamp('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_predefined_seeting');
    }
};
