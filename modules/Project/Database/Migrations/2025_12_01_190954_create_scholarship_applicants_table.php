<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('scholarship_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('student_name')->nullable();
            $table->string('university')->nullable();
            $table->string('program')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->json('documents')->nullable();
            $table->decimal('requested_amount', 14, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scholarship_applicants');
    }
};
