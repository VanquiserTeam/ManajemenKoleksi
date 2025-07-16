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
        // Rename table dari koleksi ke registrasi
        Schema::rename('koleksi', 'registrasi');
        
        // Update struktur field
        Schema::table('registrasi', function (Blueprint $table) {
            // Drop field yang tidak diperlukan
            $table->dropColumn([
                'no_registrasi',
                'no_inventaris', 
                'tahun_perolehan',
                'asal_perolehan',
                'asal_daerah',
                'jenis_koleksi',
                'lokasi_penyimpanan',
                'deskripsi',
                'status'
            ]);
            
            // Rename dan modify existing fields
            $table->renameColumn('nama_koleksi', 'nama_koleksi');
            $table->renameColumn('cara_perolehan', 'cara_perolehan');
            $table->renameColumn('panjang', 'panjang');
            $table->renameColumn('lebar', 'lebar');
            $table->renameColumn('tinggi', 'tinggi');
            $table->renameColumn('warna', 'warna');
            $table->renameColumn('bentuk', 'bentuk');
            $table->renameColumn('foto', 'foto');
            $table->renameColumn('bahan', 'bahan');
            $table->renameColumn('narasumber', 'narasumber');
        });
        
        // Add new fields
        Schema::table('registrasi', function (Blueprint $table) {
            $table->string('registrasi_id')->unique()->after('id')->comment('Format: MTP.2020.0001');
            $table->year('tahun')->after('nama_koleksi');
            $table->decimal('berat', 8, 2)->nullable()->after('tinggi')->comment('dalam gram');
            $table->string('asal')->nullable()->after('berat');
            $table->string('tanah')->nullable()->after('bahan');
            
            // Modify cara_perolehan to enum
            $table->enum('cara_perolehan', ['hibah', 'pembelian', 'peminjaman', 'warisan', 'hadiah'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert field changes
        Schema::table('registrasi', function (Blueprint $table) {
            $table->dropColumn(['registrasi_id', 'tahun', 'berat', 'asal', 'tanah']);
            
            // Add back dropped fields
            $table->string('no_registrasi')->after('id');
            $table->string('no_inventaris')->nullable()->after('no_registrasi');
            $table->year('tahun_perolehan')->after('nama_koleksi');
            $table->string('asal_perolehan')->after('cara_perolehan');
            $table->string('asal_daerah')->after('tinggi');
            $table->string('jenis_koleksi')->after('narasumber');
            $table->string('lokasi_penyimpanan')->after('jenis_koleksi');
            $table->text('deskripsi')->nullable()->after('lokasi_penyimpanan');
            $table->string('status')->default(0)->after('deskripsi');
            
            // Change cara_perolehan back to string
            $table->string('cara_perolehan')->change();
        });
        
        // Rename table back
        Schema::rename('registrasi', 'koleksi');
    }
};
