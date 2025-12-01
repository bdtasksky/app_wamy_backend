<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orphan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete()->unique();
            // We store multiple orphans per project in separate rows if needed.
            // If you prefer multiple rows per project, remove ->unique() above.
            $table->string('child_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->json('medical_needs')->nullable();
            $table->string('education_status')->nullable();
            $table->decimal('monthly_support_required', 14, 2)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orphan_details');
    }
};
