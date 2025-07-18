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
        Schema::create('jenis_koleksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 2)->unique()->comment('Kode jenis koleksi (01-10)');
            $table->string('nama', 100)->comment('Nama jenis koleksi');
            $table->text('deskripsi')->nullable()->comment('Deskripsi jenis koleksi');
            $table->boolean('status')->default(true)->comment('Status aktif/nonaktif');
            $table->timestamps();

            $table->index('kode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_koleksis');
    }
};
