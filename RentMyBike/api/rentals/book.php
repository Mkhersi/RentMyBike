<?php
// api/rentals/book.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Please log in to continue']);
    exit;
}

// Validate POST
$bike_id    = $_POST['bike_id']    ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date']   ?? '';
if (!ctype_digit($bike_id) || !$start_date || !$end_date) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'There are some missing or invalid parameters. Please retry.']);
    exit;
}

// Date validation
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start_date)
 || !preg_match('/^\d{4}-\d{2}-\d{2}$/',$end_date)
 || $end_date < $start_date) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Error - Dates are invalid. Please retry.']);
    exit;
}

// Fetch rate
try {
    $stmt = $pdo->prepare("SELECT rental_rate FROM bikes WHERE bike_id=:id LIMIT 1");
    $stmt->execute(['id'=>$bike_id]);
    $bike = $stmt->fetch();
    if (!$bike) {
        http_response_code(404);
        throw new Exception('We could not find that bike. Please retry');
    }
    $rate = (float)$bike['rental_rate'];
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'We were unable to retrieve the rental rate. Please retry.']);
    exit;
}

// Calculate days (inclusive)
$start = new DateTime($start_date);
$end   = new DateTime($end_date);
$days  = $end->diff($start)->days + 1;
$total = $days * $rate;

// Insert rental
try {
    $sql = "INSERT INTO rentals 
      (bike_id,user_id,start_date,end_date,total_rate) 
      VALUES (:bid,:uid,:s,:e,:t)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      'bid' => $bike_id,
      'uid' => $_SESSION['user_id'],
      's'   => $start_date,
      'e'   => $end_date,
      't'   => $total
    ]);
    echo json_encode(['success'=>true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'There was a database error. Please retry.']);
}
