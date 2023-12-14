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
        Schema::create('surat_keputusan_pemberhentian', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable()->unique();
            $table->date('tanggal_surat')->default(now());
            $table->string('nomor_berita_acara');
            $table->date('tanggal_berita_acara');
            $table->unsignedBigInteger('employee_id');
            $table->string('pemberhentian_dalam_jabatan');
            $table->string('dikembalikan_ke_jabatan');
            $table->text('keterangan_tambahan')->default("");
            $table->date('tanggal_berlaku');
            $table->unsignedBigInteger('signer_id');
            $table->string('signer_position');
            $table->enum('signature_type', ['manual', 'qrcode', 'digital', 'gambar tanda tangan'])->default('manual');
            $table->unsignedBigInteger('letter_template_id');
            $table->string('signed_file')->nullable();
            $table->string('signed_file_docx')->nullable();
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
        Schema::dropIfExists('surat_keputusan_pemberhentian');
    }
};
