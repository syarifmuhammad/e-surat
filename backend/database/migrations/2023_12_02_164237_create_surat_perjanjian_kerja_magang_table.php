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
        Schema::create('surat_perjanjian_kerja_magang', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable()->unique();
            $table->date('tanggal_surat')->default(now());
            $table->unsignedBigInteger('employee_id');
            $table->date('mulai_berlaku');
            $table->date('akhir_berlaku');
            $table->json('tugas');
            $table->string('tempat_kerja');
            $table->integer('upah');
            $table->string('penanggung_pembayaran');
            $table->json('rekening');
            $table->unsignedBigInteger('signer_id');
            $table->string('signer_position');
            $table->enum('signature_type', ['manual', 'digital', 'gambar tanda tangan'])->default('manual');
            $table->unsignedBigInteger('letter_template_id');
            $table->boolean('is_signed')->default(false);
            $table->string('signed_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->noActionOnDelete();
            $table->foreign('signer_id')->references('id')->on('employees')->noActionOnUpdate();
            $table->foreign('letter_template_id')->references('id')->on('letter_templates')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perjanjian_kerja_magang');
    }
};
