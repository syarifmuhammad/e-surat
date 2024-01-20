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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('position');
            $table->boolean('is_signer')->default(false);
            $table->string('signed_file')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->morphs('approvable');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
