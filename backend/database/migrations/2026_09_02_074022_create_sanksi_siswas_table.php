<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanksi_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sanksi_id')->constrained('sanksis')->onDelete('cascade');
            $table->enum('tipe_sanksi', ['individu', 'kelompok']);
            $table->text('alasan');
            $table->enum('status_penyelesaian', ['belum', 'selesai'])->default('belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanksi_siswas');
    }
};