<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Database Koleksi Museum Tanah dan Pertanian</title>

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
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        .header-gradient {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            position: relative;
            overflow: hidden;
        }
        .header-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        .museum-logo {
            background: linear-gradient(135deg, #ffffff, #ebebeb);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(46,204,113,0.3);
        }
        .card-collection {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            text-decoration: none;
            position: relative;
        }
        .card-collection::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2ecc71, #27ae60, #f39c12, #e67e22);
        }
        .card-collection:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .icon-wrapper {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2ecc71 0%, #f39c12 100%);
            margin: 15px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }
        .icon-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s;
            opacity: 0;
        }
        .card-collection:hover .icon-wrapper::before {
            animation: shine 0.6s ease-in-out;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
        }
        .icon-wrapper i {
            font-size: 2.5rem;
            color: white;
            z-index: 2;
            position: relative;
        }
        .collection-title {
            background: linear-gradient(135deg, #111111 0%, #2b2928 100%);
            color: white;
            margin: 0 15px 15px 15px;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
        }
        .collection-count {
            font-size: 2rem;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 15px;
        }
        .nav-modern {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin: 15px;
            overflow: hidden;
        }
        .nav-modern .nav-link {
            color: rgba(255,255,255,0.9);
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 5px;
        }
        .nav-modern .nav-link:hover,
        .nav-modern .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateY(-2px);
        }
        .social-buttons .btn {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        .social-buttons .btn:hover {
            transform: translateY(-3px);
        }
        .footer-section {
            background: linear-gradient(135deg, #27ae60 0%, #e67e22 100%);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
            border-radius: 20px 20px 0 0;
        }
        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #2ecc71, #f39c12, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <header class="header-gradient text-white py-5">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="museum-logo">
                                {{-- <i class="fas fa-seedling fa-2x text-white"></i> --}}
                                <img src="{{ asset('assets/img/logo-fr.png') }}" alt="Museum Logo" class="img-fluid">
                            </div>
                        </div>
                        <div class="col">
                            <h1 class="display-5 mb-0 fw-bold">DATABASE KOLEKSI</h1>
                            <h2 class="h3 mb-0 text-warning">MUSEUM TANAH DAN PERTANIAN</h2>
                            <p class="mt-2 mb-0 opacity-75">Preservasi Digital Warisan Agraris Indonesia</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="text-center text-lg-end">
                        <div class="social-buttons mb-3">
                            <a href="#" class="btn btn-outline-light"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-light"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-outline-light"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-light"><i class="fab fa-youtube"></i></a>
                        </div>

                    </div>
                </div>
            </div>

            <nav class="nav-modern mt-4">
                <div class="container-fluid">
                    <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link active" href="/">🏠 Beranda</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'geologika') }}">🗻 Geologika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'biologika') }}">🌿 Biologika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'etnografika') }}">🎭 Etnografika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'arkeologika') }}">🏛️ Arkeologika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'historika') }}">📜 Historika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'numismatika') }}">🪙 Numismatika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'filologika') }}">📚 Filologika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'keramonologika') }}">🏺 Keramonologika</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'seni-rupa') }}">🎨 Seni Rupa</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('koleksi.jenis', 'teknologika') }}">⚙️ Teknologika</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="section-divider"></div>

    <div class="content py-5">
        <div class="container">
            <!-- Hero Section -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-6 fw-bold mb-3" style="color: #2c3e50;">Jelajahi Koleksi Digital Kami</h2>
                    <p class="lead text-muted mb-4">
                        Temukan ribuan artefak bersejarah yang telah didigitalkan dengan teknologi terdepan
                    </p>
                </div>
            </div>

            <!-- Collection Grid -->
            <div class="row g-4">
                @php
                $icons = [
                    'Geologika' => 'fas fa-mountain',
                    'Biologika' => 'fas fa-leaf',
                    'Etnografika' => 'fas fa-users',
                    'Arkeologika' => 'fas fa-monument',
                    'Historika' => 'fas fa-scroll',
                    'Numismatika' => 'fas fa-coins',
                    'Filologika' => 'fas fa-book-open',
                    'Keramonologika' => 'fas fa-wine-bottle',
                    'Seni Rupa' => 'fas fa-palette',
                    'Teknologika' => 'fas fa-cogs',
                ];
                @endphp

                @foreach($jenisKoleksiData as $jenis)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <a href="{{ route('koleksi.jenis', str_replace(' ', '-', strtolower($jenis->nama))) }}" class="card-collection d-block text-decoration-none" title="{{ $jenis->deskripsi }}">
                        <div class="icon-wrapper">
                            <i class="{{ $icons[$jenis->nama] ?? 'fas fa-archive' }}"></i>
                        </div>
                        <div class="collection-title text-center">{{ $jenis->nama }}</div>
                        <div class="collection-count text-center">{{ $jenis->inventarisasis_count }}</div>
                    </a>
                </div>
                @endforeach

                <!-- Featured Stats -->
                <div class="col-lg-4 col-md-6">
                    <div class="card-collection h-100 d-flex flex-column justify-content-center">
                        <div class="text-center p-4">
                            <i class="fas fa-chart-line fa-3x text-secondary mb-3"></i>
                            <h4 class="fw-bold text-secondary">Total Koleksi</h4>
                            <h2 class="display-4 fw-bold text-dark">{{ $jenisKoleksiData->sum('inventarisasis_count') }}</h2>
                            <p class="text-muted">Artefak Terdigitalkan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>


        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <img src="{{ asset('assets/img/logo-museum-fr.png') }}" alt="Museum Logo" height="70px" class="logo-footer mb-3">
                    <h4 class="fw-bold mb-3">MUSEUM TANAH DAN PERTANIAN</h4>
                    <p class="mb-4 opacity-75">
Jl. Ir. H. Juanda No.98, RT.01/RW.01, Gudang, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat 16123
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
    // Efek hover pada kartu koleksi
    document.querySelectorAll('.card-collection').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // untuk smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation
    window.addEventListener('load', function() {
        document.querySelector('.main-wrapper').style.opacity = '1';
        document.querySelector('.main-wrapper').style.transform = 'translateY(0)';
    });
</script>

</body>
</html>
