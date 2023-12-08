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
            $table->string('reference_number')->nullable()->unique();
            $table->string('nomor_surat_sebelumnya')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->string('profesi');
            $table->string('jabatan_fungsional');
            $table->string('fakultas');
            $table->string('singkatan_fakultas');
            $table->string('jurusan');
            $table->date('mulai_berlaku');
            $table->date('akhir_berlaku');
            $table->json('rekening');
            $table->unsignedBigInteger('signer_id');
            $table->string('signer_position');
            $table->enum('signature_type', ['manual', 'qrcode', 'digital'])->default('manual');
            $table->unsignedBigInteger('letter_template_id');
            $table->string('tmp_file')->nullable();
            $table->string('signed_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('employee_id', 'pks_dosen_full_time_employee_id')->references('id')->on('employees')->noActionOnDelete();
            $table->foreign('signer_id', 'pks_dosen_full_time_signer_id')->references('id')->on('employees')->noActionOnUpdate();
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
