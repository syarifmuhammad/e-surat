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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat')->default("");
            $table->string('npwp')->default("");
            $table->enum('profesi', ['dosen', 'tpa'])->default('dosen');
            $table->string('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
