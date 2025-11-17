<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in()) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: catalog.php');
    exit();
}

$book_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check access
if (!can_access_book($user_id, $book_id)) {
    header('Location: book_detail.php?id=' . $book_id);
    exit();
}

// Get book details
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    header('Location: catalog.php');
    exit();
}

// Get or create reading history
$query = "SELECT * FROM reading_history WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$history = $stmt->get_result()->fetch_assoc();

$current_page = $history ? $history['last_page'] : 1;

// Update reading history
if ($history) {
    $query = "UPDATE reading_history SET last_read = NOW() WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
} else {
    $query = "INSERT INTO reading_history (user_id, book_id, last_page) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membaca: <?= $book['title'] ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #1a1a1a;
        }
        
        .reader-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .reader-header {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 1rem;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .reader-content {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 16px;
            min-height: 70vh;
            line-height: 2;
            font-size: 1.1rem;
        }
        
        .reader-controls {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }
        
        .page-number {
            background: var(--hover-bg);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="reader-container">
        <!-- Header -->
        <div class="reader-header">
            <div>
                <h3 style="margin-bottom: 0.3rem;"><?= $book['title'] ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">oleh <?= $book['author'] ?></p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span class="page-number">Halaman <span id="currentPage"><?= $current_page ?></span> / 100</span>
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        <!-- Content -->
        <div class="reader-content" id="bookContent">
            <h2 style="text-align: center; margin-bottom: 2rem;">
                <?= $book['title'] ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 3rem;">
                oleh <?= $book['author'] ?>
            </p>
            
            <div id="pageContent">
                <!-- Konten halaman akan dimuat di sini -->
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>
                <p>
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
                    Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
                <p>
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, 
                    eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                </p>
                <p>
                    Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos 
                    qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, 
                    adipisci velit.
                </p>
                <p style="font-style: italic; text-align: center; margin-top: 3rem; color: var(--text-secondary);">
                    Ini adalah konten demo. Di implementasi nyata, konten akan dimuat dari file PDF menggunakan PDF.js
                </p>
            </div>
        </div>

        <!-- Controls -->
        <div class="reader-controls">
            <button class="btn btn-secondary" onclick="previousPage()" id="prevBtn">
                ← Halaman Sebelumnya
            </button>
            <button class="btn btn-primary" onclick="nextPage()" id="nextBtn">
                Halaman Selanjutnya →
            </button>
        </div>
    </div>

    <script>
        let currentPage = <?= $current_page ?>;
        const totalPages = 100;
        const bookId = <?= $book_id ?>;
        const userId = <?= $user_id ?>;

        function updatePage() {
            document.getElementById('currentPage').textContent = currentPage;
            
            // Update buttons
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
            
            // Save progress
            saveProgress();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePage();
                loadPageContent();
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePage();
                loadPageContent();
            }
        }

        function loadPageContent() {
            // Di implementasi nyata, ini akan memuat konten dari PDF
            document.getElementById('pageContent').innerHTML = `
                <p>Ini adalah halaman ${currentPage} dari buku ${<?= json_encode($book['title']) ?>}.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Konten halaman ${currentPage}...</p>
                <p>Di implementasi nyata, konten ini akan dimuat dari file PDF menggunakan PDF.js library.</p>
            `;
        }

        function saveProgress() {
            // Save reading progress via AJAX
            fetch('../api/save_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    book_id: bookId,
                    last_page: currentPage
                })
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousPage();
            } else if (e.key === 'ArrowRight') {
                nextPage();
            }
        });

        // Update initial state
        updatePage();
    </script>
</body>
</html>