<?php

namespace App\Http\Controllers;

use App\Models\Inventarisasi;
use App\Models\JenisKoleksi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil semua jenis koleksi aktif dari database dengan jumlah koleksi
        $jenisKoleksiData = JenisKoleksi::aktif()
            ->withCount('inventarisasis')
            ->orderBy('kode')
            ->get();

        return view('home', compact('jenisKoleksiData'));
    }

    public function showByJenis($jenis)
    {
        // Normalisasi nama jenis untuk pencarian
        $jenisNormalized = str_replace(['-', '_'], ' ', $jenis);

        // Cari jenis koleksi berdasarkan nama (case insensitive)
        $jenisKoleksiData = JenisKoleksi::aktif()
            ->whereRaw('LOWER(nama) = ?', [strtolower($jenisNormalized)])
            ->first();

        if (!$jenisKoleksiData) {
            abort(404, 'Jenis koleksi tidak ditemukan');
        }

        $inventarisasi = Inventarisasi::with(['registrasi', 'jenisKoleksiDetail'])
            ->where('jenis_koleksi', $jenisKoleksiData->kode)
            ->paginate(12);

        $jenisNama = $jenisKoleksiData->nama;

        return view('koleksi.jenis', compact('inventarisasi', 'jenisNama', 'jenis', 'jenisKoleksiData'));
    }
}
