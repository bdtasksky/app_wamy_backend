<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('mosque_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete()->unique();
            $table->string('construction_stage')->nullable();
            $table->decimal('estimated_cost', 14, 2)->nullable();
            $table->string('land_area')->nullable();
            $table->json('main_materials')->nullable(); // array of {name,qty,unit,unit_price}
            $table->string('architect_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mosque_details');
    }
};
