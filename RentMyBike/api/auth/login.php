<?php
// api/auth/login.php
header('Content-Type: application/json; charset=utf-8');
ob_start();
session_start();

// correct path: two levels up
$DB_PATH = __DIR__ . '/../../config/db.php';
if (!file_exists($DB_PATH)) {
  http_response_code(500);
  ob_clean();
  echo json_encode(['success'=>false,'error'=>'DB config not found']);
  exit;
}
require $DB_PATH;

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
  http_response_code(400);
  ob_clean();
  echo json_encode(['success'=>false,'error'=>'We need your email and password to continue.']);
  exit;
}

try {
    $stmt = $pdo->prepare("SELECT user_id, username, password FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();

  if ( !$u || !password_verify($password, $u['password'])) {
    //invalid
    http_response_code(401);
    ob_clean();
    echo json_encode(['success'=>false,'error'=>'Please check your credentials']);
    exit;
  }

  $_SESSION['user_id']  = (int)$u['user_id'];
  $_SESSION['username'] = $u['username'];

  ob_clean();
  echo json_encode(['success'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  ob_clean();
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
