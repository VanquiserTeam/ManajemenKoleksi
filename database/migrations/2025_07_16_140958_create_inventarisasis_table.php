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
        Schema::create('inventarisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koleksi_id')->constrained('koleksi')->onDelete('cascade');
            $table->string('nomor_inventarisasi')->unique()->comment('Format: 01.B.2020.0001');
            $table->enum('status_kepemilikan', [
                'milik_museum', 
                'peminjaman_jangka_pendek', 
                'peminjaman_jangka_panjang', 
                'bmn'
            ]);
            $table->enum('jenis_koleksi', [
                '01', '02', '03', '04', '05', 
                '06', '07', '08', '09', '10'
            ])->comment('01=Geologika, 02=Biologika, 03=Etnografika, 04=Arkeologika, 05=Historika, 06=Numismatika, 07=Filologika, 08=Keramonologika, 09=Seni Rupa, 10=Teknologika');
            $table->enum('kondisi_fisik', ['baik', 'rusak', 'hilang']);
            $table->enum('solusi', [
                'konservasi_ringan', 
                'konservasi_sedang', 
                'konservasi_berat'
            ])->nullable()->comment('Diisi jika kondisi fisik rusak');
            $table->string('lokasi_penyimpanan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarisasis');
    }
};
