<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandidat_id')->constrained('kandidats')->onDelete('cascade');
            $table->decimal('skor_ahp', 8, 5);
            $table->decimal('skor_rf', 8, 5);
            $table->decimal('skor_akhir', 8, 5);
            $table->integer('ranking');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};