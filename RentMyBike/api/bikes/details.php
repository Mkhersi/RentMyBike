<?php
// api/bikes/details.php
// Returns JSON details for a single bike by bike_id

header('Content-Type: application/json');
require __DIR__ . '/../../config/db.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'You have not selected a bike. Please select one to continue']);
    exit;
}

$bike_id = (int)$_GET['id'];

try {
    $sql = "SELECT 
                b.bike_id,
                b.user_id,
                b.make,
                b.model,
                b.bike_type,
                b.frame_size,
                b.gear_count,
                b.year,
                b.location,
                b.rental_rate,
                b.condition,
                b.image_url,
                u.username AS owner_name
            FROM bikes b
            JOIN users u ON b.user_id = u.user_id
            WHERE b.bike_id = :id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $bike_id]);
    $bike = $stmt->fetch();

    if (!$bike) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Sorry we cannot find this bike. Please retry.']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => $bike]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'There is a database error occurring. Please retry.']);
}
