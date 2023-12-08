<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pertelaan_perjanjian_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_dosen_full_time_id');
            $table->string('pendidikan');
            $table->string('jangka_waktu');
            $table->string('tahun_satu');
            $table->integer('tunjangan_dasar_satu');
            $table->integer('tunjangan_fungsional_satu');
            $table->integer('tunjangan_struktural_satu');
            $table->integer('tunjangan_kemahalan_satu');
            $table->integer('pendapatan_bulanan_satu');
            $table->string('tahun_dua');
            $table->integer('tunjangan_dasar_dua');
            $table->integer('tunjangan_fungsional_dua');
            $table->integer('tunjangan_struktural_dua');
            $table->integer('tunjangan_kemahalan_dua');
            $table->integer('pendapatan_bulanan_dua');
            $table->json('fasilitas_lainnya');
            $table->timestamps();

            $table->foreign('surat_dosen_full_time_id')->references('id')->on('surat_perjanjian_kerja_dosen_full_time')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertelaan_perjanjian_kerja');
    }
};
