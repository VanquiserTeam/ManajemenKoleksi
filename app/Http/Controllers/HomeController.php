<?php

namespace App\Http\Controllers;

use App\Models\Inventarisasi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil jenis koleksi dari model Inventarisasi
        $jenisKoleksiOptions = Inventarisasi::getJenisKoleksiOptions();

        // Menghitung jumlah koleksi berdasarkan jenis dari inventarisasi
        $jenisKoleksi = [];
        foreach ($jenisKoleksiOptions as $kode => $label) {
            // Mengambil nama jenis koleksi tanpa kode (contoh: "01 - Geologika" menjadi "Geologika")
            $namaJenis = trim(explode(' - ', $label)[1]);
            $jenisKoleksi[$namaJenis] = Inventarisasi::where('jenis_koleksi', $kode)->count();
        }

        return view('home', compact('jenisKoleksi'));
    }

    public function showByJenis($jenis)
    {
        // Mapping nama jenis ke kode
        $jenisKoleksiOptions = Inventarisasi::getJenisKoleksiOptions();
        $kodeJenis = null;

        foreach ($jenisKoleksiOptions as $kode => $label) {
            $namaJenis = trim(explode(' - ', $label)[1]);
            if (strtolower($namaJenis) === strtolower($jenis)) {
                $kodeJenis = $kode;
                break;
            }
        }

        if (!$kodeJenis) {
            abort(404, 'Jenis koleksi tidak ditemukan');
        }

        $inventarisasi = Inventarisasi::with('registrasi')
            ->where('jenis_koleksi', $kodeJenis)
            ->paginate(12);

        $jenisNama = trim(explode(' - ', $jenisKoleksiOptions[$kodeJenis])[1]);

        return view('koleksi.jenis', compact('inventarisasi', 'jenisNama', 'jenis'));
    }
}
