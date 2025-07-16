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
        Schema::create('koleksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi');
            $table->string('no_inventaris')->nullable();
            $table->string('nama_koleksi');
            $table->year('tahun_perolehan');
            $table->string('cara_perolehan');
            $table->string('asal_perolehan');
            $table->decimal('panjang', 8, 2)->nullable(); // dalam cm
            $table->decimal('lebar', 8, 2)->nullable(); // dalam cm
            $table->decimal('tinggi', 8, 2)->nullable(); // dalam cm
            $table->string('asal_daerah');
            $table->string('warna')->nullable();
            $table->string('bentuk')->nullable();
            $table->string('foto')->nullable(); // path to photo file
            $table->string('bahan')->nullable();
            $table->string('narasumber')->nullable();
            $table->string('jenis_koleksi');
            $table->string('lokasi_penyimpanan');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koleksi');
    }
};
