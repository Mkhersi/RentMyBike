<?php
// api/rentals/list.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Please log in to continue.']);
    exit;
}

$uid   = $_SESSION['user_id'];
$today = date('Y-m-d');

try {
    $stmt = $pdo->prepare("SELECT r.rental_id, r.bike_id, r.start_date, r.end_date, r.total_rate,
                                  b.make, b.model, b.image_url
                           FROM rentals r
                           JOIN bikes b ON r.bike_id = b.bike_id
                           WHERE r.user_id = :uid
                           ORDER BY r.start_date DESC");
    $stmt->execute(['uid'=>$uid]);
    $all = $stmt->fetchAll();

    $upcoming = []; $past = [];
    foreach ($all as $r) {
        if ($r['end_date'] >= $today) {
            $upcoming[] = $r;
        } else {
            $past[] = $r;
        }
    }
    echo json_encode(['success'=>true,'upcoming'=>$upcoming,'past'=>$past]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'There was a database error. Please retry.']);
}
