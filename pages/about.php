<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Perpustakaan Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 100px;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        .about-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .feature-box {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            background: #f8f9fa;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 3rem;
            color: #6366f1;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="margin-bottom: 50px;">
        <div class="text-center text-white mb-5">
            <h1 class="display-4"><i class="bi bi-info-circle"></i> Tentang Kami</h1>
            <p class="lead">Perpustakaan Digital Terbaik untuk Anda</p>
        </div>

        <div class="about-card">
            <h2 class="mb-4">Selamat Datang di Perpustakaan Online</h2>
            <p class="lead">
                Perpustakaan Online adalah platform perpustakaan digital yang menyediakan akses ke ribuan buku berkualitas tinggi dalam berbagai kategori. Kami berkomitmen untuk menyediakan pengalaman membaca terbaik bagi semua pengguna.
            </p>
            
            <hr class="my-4">
            
            <h3 class="mb-4">Visi & Misi</h3>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="bi bi-eye-fill text-primary"></i> Visi</h5>
                    <p>Menjadi platform perpustakaan digital terdepan yang memberikan akses mudah dan terjangkau terhadap pengetahuan dan informasi berkualitas untuk semua kalangan.</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="bi bi-bullseye text-primary"></i> Misi</h5>
                    <ul>
                        <li>Menyediakan koleksi buku digital yang lengkap dan berkualitas</li>
                        <li>Memberikan pengalaman membaca yang nyaman dan menyenangkan</li>
                        <li>Mendorong budaya literasi di Indonesia</li>
                        <li>Memfasilitasi akses pengetahuan untuk semua orang</li>
                    </ul>
                </div>
            </div>
        </div>

        <h3 class="text-white text-center mb-4">Mengapa Memilih Kami?</h3>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-book-fill feature-icon"></i>
                    <h5>Koleksi Lengkap</h5>
                    <p>Ribuan buku dari berbagai kategori dan genre tersedia untuk Anda</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-phone-fill feature-icon"></i>
                    <h5>Akses Mudah</h5>
                    <p>Baca kapan saja, di mana saja melalui perangkat apa pun</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-wallet-fill feature-icon"></i>
                    <h5>Harga Terjangkau</h5>
                    <p>Paket langganan dengan harga yang ramah di kantong</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-shield-check feature-icon"></i>
                    <h5>Aman & Terpercaya</h5>
                    <p>Data Anda aman dengan sistem keamanan terbaik</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-lightning-fill feature-icon"></i>
                    <h5>Loading Cepat</h5>
                    <p>Teknologi modern untuk pengalaman membaca tanpa lag</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-headset feature-icon"></i>
                    <h5>Support 24/7</h5>
                    <p>Tim support kami siap membantu Anda kapan saja</p>
                </div>
            </div>
        </div>

        <div class="about-card mt-4">
            <h3 class="mb-4">Hubungi Kami</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><i class="bi bi-geo-alt-fill text-primary"></i> <strong>Alamat:</strong></p>
                    <p>Jl. Pendidikan No. 123, Jakarta Selatan, Indonesia</p>
                    
                    <p><i class="bi bi-telephone-fill text-primary"></i> <strong>Telepon:</strong></p>
                    <p>+62 812-3456-7890</p>
                </div>
                <div class="col-md-6">
                    <p><i class="bi bi-envelope-fill text-primary"></i> <strong>Email:</strong></p>
                    <p>info@perpustakaan.com</p>
                    
                    <p><i class="bi bi-clock-fill text-primary"></i> <strong>Jam Operasional:</strong></p>
                    <p>Senin - Jumat: 08:00 - 17:00 WIB<br>
                    Sabtu: 09:00 - 15:00 WIB<br>
                    Minggu: Libur</p>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="mailto:info@perpustakaan.com" class="btn btn-primary me-2">
                    <i class="bi bi-envelope"></i> Email Kami
                </a>
                <a href="tel:+6281234567890" class="btn btn-success">
                    <i class="bi bi-telephone"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>