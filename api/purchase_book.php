<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = (int)$input['book_id'];

// Check if book exists
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
    exit();
}

// Check if already purchased
$query = "SELECT * FROM purchases WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already purchased']);
    exit();
}

// Insert purchase
$query = "INSERT INTO purchases (user_id, book_id, amount) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iid", $user_id, $book_id, $book['price']);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Purchase successful',
        'book_id' => $book_id,
        'amount' => $book['price']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Purchase failed']);
}
?>