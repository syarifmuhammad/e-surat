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
        Schema::create('surat_perjanjian_kerja_dosen_full_time', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_private')->default(false);
            $table->string('reference_number')->nullable()->unique();
            $table->date('tanggal_surat')->default(now());
            $table->date('mulai_berlaku')->nullable()->default(null);
            $table->bigInteger('masa_berlaku');
            $table->string('nomor_surat_sebelumnya')->nullable();
            $table->date('tanggal_surat_sebelumnya');
            $table->unsignedBigInteger('employee_id');
            $table->string('jabatan_fungsional');
            $table->json('prodi');
            $table->json('rekening');
            $table->enum('signature_type', ['manual', 'digital', 'gambar tanda tangan'])->default('digital');
            $table->unsignedBigInteger('letter_template_id');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_signed')->default(false);

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('employee_id', 'pks_dosen_full_time_employee_id')->references('id')->on('employees')->noActionOnDelete();
            $table->foreign('letter_template_id', 'pks_dosen_full_time_letter_template_id')->references('id')->on('letter_templates')->restrictOnDelete();
            $table->foreign('created_by', 'pks_dosen_full_time_created_by')->references('id')->on('users')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perjanjian_kerja_dosen_full_time');
    }
};
