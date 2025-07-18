<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $jenisNama }} - Database Koleksi Museum</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #2ecc71 0%, #f39c12 100%);
            min-height: 100vh;
        }

        .main-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            margin: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-gradient {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 30px 0;
        }

        .koleksi-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .koleksi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .koleksi-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .placeholder-img {
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="main-wrapper">
        <header class="header-gradient text-white">
            <div class="container">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jenisNama }}</li>
                    </ol>
                </nav>

                <!-- Header Section -->
                <div class="text-center">
                    <h1 class="display-5 fw-bold mb-3">Koleksi {{ $jenisNama }}</h1>
                    @if(isset($jenisKoleksiData) && $jenisKoleksiData->deskripsi)
                    <p class="lead mb-3 opacity-90">{{ $jenisKoleksiData->deskripsi }}</p>
                    @endif
                    <p class="lead mb-0 opacity-75">
                        Menampilkan {{ $inventarisasi->total() }} koleksi {{ strtolower($jenisNama) }} dari database
                        museum
                    </p>
                </div>
            </div>
        </header>

        <div class="container py-4">
            <!-- Filter dan Search -->
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari koleksi..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="kondisiFilter">
                            <option value="">🔍 Semua Kondisi</option>
                            <option value="baik">✅ Kondisi Baik</option>
                            <option value="rusak">⚠️ Rusak</option>
                            <option value="hilang">❌ Hilang</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Koleksi Grid -->
            <div class="row g-4" id="koleksiGrid">
                @forelse($inventarisasi as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6 koleksi-item">
                        <div class="card koleksi-card h-100">
                            @if ($item->registrasi && $item->registrasi->foto)
                                <img src="{{ asset('storage/' . $item->registrasi->foto) }}" class="card-img-top"
                                    alt="{{ $item->registrasi->nama_koleksi }}">
                            @else
                                <div class="placeholder-img">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold">
                                    {{ $item->registrasi->nama_koleksi ?? 'Nama Tidak Tersedia' }}</h6>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-barcode"></i> {{ $item->nomor_inventarisasi }}
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        <span class="badge bg-primary">{{ $jenisNama }}</span>
                                        @if ($item->kondisi_fisik)
                                            <span
                                                class="badge {{ $item->kondisi_fisik === 'baik' ? 'bg-success' : ($item->kondisi_fisik === 'rusak' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $item->kondisi_fisik === 'baik' ? '✅' : ($item->kondisi_fisik === 'rusak' ? '⚠️' : '❌') }}
                                                {{ ucfirst($item->kondisi_fisik) }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($item->registrasi && $item->registrasi->tahun)
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-calendar"></i> Tahun {{ $item->registrasi->tahun }}
                                        </div>
                                    @endif

                                    @if ($item->lokasi_penyimpanan)
                                        <div class="small text-muted">
                                            <i class="fas fa-map-marker-alt"></i> {{ $item->lokasi_penyimpanan }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-box-open fa-5x text-muted opacity-50"></i>
                            </div>
                            <h4 class="text-muted">Belum Ada Koleksi</h4>
                            <p class="text-muted">Belum ada koleksi {{ strtolower($jenisNama) }} yang tersedia dalam
                                database.</p>
                            {{-- <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                    </a> --}}
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($inventarisasi->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $inventarisasi->links() }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Back to Home -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer-section bg-dark text-white text-center py-4 mt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <img src="{{ asset('assets/img/logo-museum-fr.png') }}" alt="Museum Logo" height="70px"
                            class="logo-footer mb-3">
                        <h4 class="fw-bold mb-3">MUSEUM TANAH DAN PERTANIAN</h4>
                        <p class="mb-4 opacity-75">
                            Jl. Ir. H. Juanda No.98, RT.01/RW.01, Gudang, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat
                            16123
                        </p>
                        <div class="social-buttons mb-4">
                            <a href="#" class="btn btn-outline-light me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light me-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light me-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                        <hr class="border-light opacity-25">
                        <p class="small opacity-75 mb-0">
                            © {{ date('Y') }} Museum Tanah dan Pertanian. Semua hak dilindungi undang-undang.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Simple client-side search
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.koleksi-card');

            cards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const parent = card.closest('.col-lg-3, .col-md-4, .col-sm-6');

                if (title.includes(searchTerm)) {
                    parent.style.display = '';
                } else {
                    parent.style.display = 'none';
                }
            });
        });

        // Simple client-side filter by condition
        document.getElementById('kondisiFilter').addEventListener('change', function() {
            const selectedKondisi = this.value.toLowerCase();
            const cards = document.querySelectorAll('.koleksi-card');

            cards.forEach(card => {
                const badges = card.querySelectorAll('.badge');
                const parent = card.closest('.col-lg-3, .col-md-4, .col-sm-6');
                let hasCondition = false;

                badges.forEach(badge => {
                    if (badge.textContent.toLowerCase().includes(selectedKondisi) ||
                        selectedKondisi === '') {
                        hasCondition = true;
                    }
                });

                if (hasCondition || selectedKondisi === '') {
                    parent.style.display = '';
                } else {
                    parent.style.display = 'none';
                }
            });
        });

        // Enhanced search with multiple criteria
        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const selectedKondisi = document.getElementById('kondisiFilter').value.toLowerCase();
            const items = document.querySelectorAll('.koleksi-item');

            items.forEach(item => {
                const card = item.querySelector('.koleksi-card');
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const badges = card.querySelectorAll('.badge');

                let matchesSearch = title.includes(searchTerm) || searchTerm === '';
                let matchesCondition = selectedKondisi === '';

                badges.forEach(badge => {
                    if (badge.textContent.toLowerCase().includes(selectedKondisi)) {
                        matchesCondition = true;
                    }
                });

                if (matchesSearch && matchesCondition) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Update both search and filter to use the enhanced function
        document.getElementById('searchInput').addEventListener('input', performSearch);
        document.getElementById('kondisiFilter').addEventListener('change', performSearch);
    </script>

</body>

</html>

</body>

</html>
