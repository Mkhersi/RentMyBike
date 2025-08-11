<?php

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require __DIR__ . '/../../config/db.php';

    $q = trim($_GET['q'] ?? '');

    if ($q === '') {
        // No search term → return all bikes
        $stmt = $pdo->query("
            SELECT bike_id, make, model, bike_type, frame_size, gear_count, year,
                   location, rental_rate, `condition`, image_url
            FROM bikes
            ORDER BY bike_id DESC
        ");
    } else {
        // Use positional placeholders, binding the same term twice
        $term = "%$q%";
        $stmt = $pdo->prepare("
            SELECT bike_id, make, model, bike_type, frame_size, gear_count, year,
                   location, rental_rate, `condition`, image_url
            FROM bikes
            WHERE make  LIKE ?
               OR model LIKE ?
            ORDER BY bike_id DESC
        ");
        $stmt->execute([$term, $term]);
    }

    $bikes = $stmt->fetchAll();

    ob_clean();
    echo json_encode(['success' => true, 'data' => $bikes]);

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
