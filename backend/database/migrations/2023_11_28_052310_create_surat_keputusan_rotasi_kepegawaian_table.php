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
        Schema::create('surat_keputusan_rotasi_kepegawaian', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_private')->default(false);
            $table->string('reference_number')->nullable()->unique();
            $table->date('tanggal_surat')->default(now());
            $table->bigInteger('masa_berlaku');
            $table->string('nomor_berita_acara');
            $table->date('tanggal_berita_acara');
            $table->unsignedBigInteger('employee_id');
            $table->string('status_awal');
            $table->string('jabatan_awal');
            $table->string('status_akhir');
            $table->string('jabatan_akhir');
            $table->enum('signature_type', ['manual', 'digital', 'gambar tanda tangan'])->default('digital');
            $table->unsignedBigInteger('letter_template_id');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_signed')->default(false);
            // $table->string('signed_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->noActionOnDelete();
            $table->foreign('letter_template_id')->references('id')->on('letter_templates')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keputusan_rotasi_kepegawaian');
    }
};
