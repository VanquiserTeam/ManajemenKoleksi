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
        Schema::create('cek_kondisi_kelembapans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_cek');
            $table->time('waktu');
            $table->string('petugas_1');
            $table->string('petugas_2')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('kelembapan', 5, 2)->comment('Kelembapan dalam %');
            $table->decimal('suhu', 5, 2)->comment('Suhu dalam Celsius');
            $table->string('lokasi');
            $table->enum('status', ['rentang_ideal', 'kelembapan_tinggi', 'kelembapan_rendah']);
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cek_kondisi_kelembapans');
    }
};
