<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kandidats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollee_id')->unique();
            $table->string('nama')->nullable();
            $table->string('city')->nullable();
            $table->float('city_development_index')->nullable();
            $table->string('gender')->nullable();
            $table->string('relevent_experience')->nullable();
            $table->tinyInteger('relevent_experience_encoded')->nullable();
            $table->string('enrolled_university')->nullable();
            $table->string('education_level')->nullable();
            $table->tinyInteger('education_level_encoded')->nullable();
            $table->string('major_discipline')->nullable();
            $table->string('experience')->nullable();
            $table->integer('experience_encoded')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_type')->nullable();
            $table->string('last_new_job')->nullable();
            $table->integer('training_hours')->nullable();
            $table->tinyInteger('target')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandidats');
    }
};