<?php
// api/rentals/cancel.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Please log in to continue']);
    exit;
}

$rental_id = $_GET['id'] ?? '';
if (!ctype_digit($rental_id)) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Rental ID is invalid. Please retry.']);
    exit;
}

try {
    // Ensure owner matches
    $stmt = $pdo->prepare("DELETE FROM rentals WHERE rental_id=:rid AND user_id=:uid");
    $stmt->execute(['rid'=>$rental_id,'uid'=>$_SESSION['user_id']]);
    if ($stmt->rowCount() === 0) {
        http_response_code(403);
        throw new Exception('Not allowed or not found');
    }
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'There was an error cancelling your booking. Please retry.']);
}
