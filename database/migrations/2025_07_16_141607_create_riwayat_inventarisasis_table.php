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
        Schema::create('riwayat_inventarisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventarisasi_id')->constrained()->onDelete('cascade');
            $table->enum('kondisi_fisik_sebelum', ['baik', 'rusak', 'hilang']);
            $table->enum('kondisi_fisik_sesudah', ['baik', 'rusak', 'hilang']);
            $table->enum('solusi', [
                'konservasi_ringan', 
                'konservasi_sedang', 
                'konservasi_berat'
            ])->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_perubahan');
            $table->string('petugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_inventarisasis');
    }
};
