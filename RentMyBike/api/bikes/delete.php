<?php
// api/bikes/delete.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

if (empty($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success'=>false,'error'=>'You must log in to continue']); exit;
}
$userId = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false,'error'=>'Sorry, this action is unavailable']); exit;
}

// Accept x-www-form-urlencoded or JSON
$bikeId = $_POST['bike_id'] ?? null;
if ($bikeId === null) {
  $raw = file_get_contents('php://input');
  if ($raw) {
    $json = json_decode($raw, true);
    if (isset($json['bike_id'])) $bikeId = $json['bike_id'];
  }
}

if (!ctype_digit((string)$bikeId)) {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Invalid bike ID. Please retry.']); exit;
}

$stmt = $pdo->prepare("SELECT image_url FROM bikes WHERE bike_id=:bid AND user_id=:uid LIMIT 1");
$stmt->execute([':bid'=>$bikeId, ':uid'=>$userId]);
$image = $stmt->fetchColumn();
if ($image === false) {
  http_response_code(403);
  echo json_encode(['success'=>false,'error'=>'Sorry, we either could not find your bike or you have an ivalid bike ID. Please retry. ']); exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM bikes WHERE bike_id=:bid AND user_id=:uid LIMIT 1");
  $stmt->execute([':bid'=>$bikeId, ':uid'=>$userId]);

  if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['success'=>false,'error'=>'Sorry, we either could not find your bike or you have an ivalid bike ID. Please retry.']); exit;
  }

  // Best‑effort image delete
  if ($image) {
    $abs = __DIR__ . '/../../assets/images/' . $image;
    if (is_file($abs)) @unlink($abs);
  }

  echo json_encode(['success'=>true, 'deleted_bike_id'=>(int)$bikeId]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>'There was an error in deleting this bike. Please try again']);
}
