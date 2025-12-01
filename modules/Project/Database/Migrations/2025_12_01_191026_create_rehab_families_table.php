<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rehab_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('family_head_name')->nullable();
            $table->integer('members_count')->nullable();
            $table->json('vulnerabilities')->nullable();
            $table->decimal('monthly_expenses', 14, 2)->nullable();
            $table->json('preferred_assistance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rehab_families');
    }
};
