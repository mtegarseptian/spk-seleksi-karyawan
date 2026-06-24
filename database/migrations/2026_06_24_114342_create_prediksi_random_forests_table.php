<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi_random_forests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandidat_id')->constrained('kandidats')->onDelete('cascade');
            $table->decimal('nilai_prediksi', 6, 4); // 0.0000 - 1.0000
            $table->string('status'); // Layak / Tidak Layak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi_random_forests');
    }
};