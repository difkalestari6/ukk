<?php
session_start();
require_once '../config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Check if logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['book_id']) || !isset($input['last_page'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = (int)$input['book_id'];
$last_page = (int)$input['last_page'];

// Check if record exists
$query = "SELECT * FROM reading_history WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $query = "UPDATE reading_history SET last_page = ?, last_read = NOW() WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $last_page, $user_id, $book_id);
} else {
    // Insert new record
    $query = "INSERT INTO reading_history (user_id, book_id, last_page) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $book_id, $last_page);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Progress saved']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save progress']);
}
?>