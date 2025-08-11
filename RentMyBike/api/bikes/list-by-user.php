<?php
// api/bikes/list-by-user.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'error'=>'Please log in to continue']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $sql = "SELECT bike_id, make, model, bike_type, rental_rate, image_url
            FROM bikes
            WHERE user_id = :uid
            ORDER BY bike_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    $bikes = $stmt->fetchAll();

    echo json_encode(['success'=>true, 'data'=>$bikes]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'DB Error: ' . $e->getMessage()
    ]);
}
