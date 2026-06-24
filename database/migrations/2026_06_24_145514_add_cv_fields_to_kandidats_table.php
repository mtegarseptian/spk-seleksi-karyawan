<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kandidats', function (Blueprint $table) {
            $table->string('sumber')->default('dataset')->after('enrollee_id'); // dataset | cv
            $table->string('cv_path')->nullable()->after('sumber');
            $table->string('portfolio_path')->nullable()->after('cv_path');
            $table->string('pendidikan_cv')->nullable()->after('portfolio_path');
            $table->integer('pengalaman_tahun_cv')->nullable()->after('pendidikan_cv');
            $table->integer('sertifikasi_count')->nullable()->after('pengalaman_tahun_cv');
            $table->text('sertifikasi_list')->nullable()->after('sertifikasi_count');
            $table->integer('skill_pm_count')->nullable()->after('sertifikasi_list');
            $table->text('skill_pm_list')->nullable()->after('skill_pm_count');
            $table->tinyInteger('leadership_encoded')->nullable()->after('skill_pm_list');
            $table->text('tools_list')->nullable()->after('leadership_encoded');
            $table->integer('jumlah_proyek')->nullable()->after('tools_list');
        });
    }

    public function down(): void
    {
        Schema::table('kandidats', function (Blueprint $table) {
            $table->dropColumn([
                'sumber', 'cv_path', 'portfolio_path', 'pendidikan_cv', 'pengalaman_tahun_cv',
                'sertifikasi_count', 'sertifikasi_list', 'skill_pm_count', 'skill_pm_list',
                'leadership_encoded', 'tools_list', 'jumlah_proyek',
            ]);
        });
    }
};