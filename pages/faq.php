<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Perpustakaan Online</title>
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
        .faq-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #6366f1;
            color: white;
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="margin-bottom: 50px;">
        <div class="text-center text-white mb-5">
            <h1 class="display-4"><i class="bi bi-question-circle"></i> FAQ</h1>
            <p class="lead">Pertanyaan yang Sering Diajukan</p>
        </div>

        <div class="faq-card">
            <h3 class="mb-4">Umum</h3>
            
            <div class="accordion" id="accordionGeneral">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Apa itu Perpustakaan Online?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionGeneral">
                        <div class="accordion-body">
                            Perpustakaan Online adalah platform perpustakaan digital yang menyediakan akses ke ribuan buku dalam berbagai kategori. Anda dapat membaca buku kapan saja dan di mana saja melalui perangkat yang terhubung internet.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Bagaimana cara mendaftar?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionGeneral">
                        <div class="accordion-body">
                            Klik tombol "Register" di pojok kanan atas, isi formulir pendaftaran dengan username, email, dan password. Setelah mendaftar, Anda bisa langsung login dan mulai membaca buku gratis.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Apakah gratis untuk mendaftar?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionGeneral">
                        <div class="accordion-body">
                            Ya, pendaftaran di Perpustakaan Online sepenuhnya gratis. Anda bisa membaca semua buku dengan label "Gratis" tanpa biaya apapun. Untuk mengakses buku premium, Anda perlu berlangganan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <h3 class="mb-4">Langganan</h3>
            
            <div class="accordion" id="accordionSubscription">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Apa perbedaan paket Silver dan Gold?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionSubscription">
                        <div class="accordion-body">
                            <strong>Paket Silver (Rp 15.000):</strong> Akses semua buku premium selama 7 hari.<br>
                            <strong>Paket Gold (Rp 50.000):</strong> Akses semua buku premium selama 30 hari dengan hemat 33%!
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            Bagaimana cara berlangganan?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#accordionSubscription">
                        <div class="accordion-body">
                            Setelah login, klik menu "Langganan", pilih paket yang sesuai (Silver atau Gold), dan klik "Berlangganan Sekarang". Ikuti instruksi pembayaran yang muncul.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            Apakah langganan otomatis diperpanjang?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#accordionSubscription">
                        <div class="accordion-body">
                            Tidak, langganan kami tidak diperpanjang secara otomatis. Setelah masa langganan berakhir, Anda perlu berlangganan lagi jika ingin terus mengakses buku premium.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                            Bagaimana cara membatalkan langganan?
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#accordionSubscription">
                        <div class="accordion-body">
                            Anda dapat menghubungi customer service kami melalui halaman kontak. Namun, perlu diingat bahwa pembayaran yang sudah dilakukan tidak dapat dikembalikan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <h3 class="mb-4">Membaca Buku</h3>
            
            <div class="accordion" id="accordionReading">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                            Di perangkat apa saja saya bisa membaca?
                        </button>
                    </h2>
                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#accordionReading">
                        <div class="accordion-body">
                            Anda bisa membaca di laptop, komputer, tablet, atau smartphone. Cukup buka website kami di browser perangkat Anda dan login ke akun.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                            Apakah bisa download buku untuk dibaca offline?
                        </button>
                    </h2>
                    <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#accordionReading">
                        <div class="accordion-body">
                            Saat ini, fitur download untuk dibaca offline belum tersedia. Anda perlu koneksi internet untuk membaca buku di platform kami.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                            Bagaimana cara menambahkan buku ke favorit?
                        </button>
                    </h2>
                    <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#accordionReading">
                        <div class="accordion-body">
                            Di halaman daftar buku atau halaman detail buku, klik tombol "Tambah ke Favorit" dengan icon hati. Buku favorit Anda bisa dilihat di halaman Profil.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <h3 class="mb-4">Akun & Keamanan</h3>
            
            <div class="accordion" id="accordionAccount">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">
                            Bagaimana jika lupa password?
                        </button>
                    </h2>
                    <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#accordionAccount">
                        <div class="accordion-body">
                            Fitur reset password akan segera hadir. Untuk sementara, silakan hubungi customer service kami melalui halaman kontak dengan menyertakan email terdaftar Anda.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12">
                            Apakah data saya aman?
                        </button>
                    </h2>
                    <div id="faq12" class="accordion-collapse collapse" data-bs-parent="#accordionAccount">
                        <div class="accordion-body">
                            Ya, kami menggunakan enkripsi untuk melindungi data Anda. Password Anda di-hash dengan algoritma bcrypt dan tidak bisa dilihat oleh siapapun, termasuk admin.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="alert alert-info d-inline-block">
                <i class="bi bi-question-circle-fill"></i> 
                Masih ada pertanyaan? 
                <a href="contact.php" class="alert-link">Hubungi kami</a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>