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
        Schema::create('surat_keputusan_pemberhentian_dan_pengangkatan', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable()->unique('reference_number');
            $table->date('tanggal_surat')->default(now());
            $table->bigInteger('masa_berlaku');
            $table->string('nomor_berita_acara');
            $table->date('tanggal_berita_acara');
            $table->unsignedBigInteger('employee_id');
            $table->string('pemberhentian_dalam_jabatan');
            $table->string('pengangkatan_dalam_jabatan');
            $table->text('keterangan_tambahan')->default("");
            $table->enum('signature_type', ['manual', 'digital', 'gambar tanda tangan'])->default('digital');
            $table->unsignedBigInteger('letter_template_id');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_signed')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('employee_id', 'pemberhentian_dan_pengangkatan_employee_id')->references('id')->on('employees')->noActionOnDelete();
            $table->foreign('letter_template_id', 'pemberhentian_dan_pengangkatan_letter_template_id')->references('id')->on('letter_templates')->restrictOnDelete();
            $table->foreign('created_by', 'pemberhentian_dan_pengangkatan_created_by')->references('id')->on('users')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keputusan_pemberhentian_dan_pengangkatan');
    }
};
