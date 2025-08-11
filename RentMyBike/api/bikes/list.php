<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../../config/db.php';

$q    = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$order = "bike_id DESC"; // newest
if ($sort === 'price-asc')  $order = "CAST(rental_rate AS DECIMAL(10,2)) ASC, bike_id DESC";
if ($sort === 'price-desc') $order = "CAST(rental_rate AS DECIMAL(10,2)) DESC, bike_id DESC";

try {
  if ($q === '') {
    $stmt = $pdo->query("
      SELECT bike_id, make, model, bike_type, frame_size, gear_count, year,
             location, rental_rate, `condition`, image_url
      FROM bikes
      ORDER BY $order
    ");
  } else {
    $term = "%$q%";
    $stmt = $pdo->prepare("
      SELECT bike_id, make, model, bike_type, frame_size, gear_count, year,
             location, rental_rate, `condition`, image_url
      FROM bikes
      WHERE make LIKE ? OR model LIKE ?
      ORDER BY $order
    ");
    $stmt->execute([$term, $term]);
  }
  echo json_encode(['success'=>true, 'data'=>$stmt->fetchAll()]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'error'=>'Server error']);
}

