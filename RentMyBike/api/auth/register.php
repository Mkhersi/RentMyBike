<?php
// api/auth/register.php
header('Content-Type: application/json; charset=utf-8');
ob_start(); // buffer any stray output
session_start();

// correct path: two levels up from api/auth -> project root/config/db.php
$DB_PATH = __DIR__ . '/../../config/db.php';
if (!file_exists($DB_PATH)) {
  http_response_code(500);
  ob_clean();
  echo json_encode(['success'=>false,'error'=>'DB config not found']);
  exit;
}
require $DB_PATH;

// inputs
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm']  ?? '';

$errors = [];
if ($username === '') $errors[] = 'Your username is required to proceed.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Your valid email is required.';
if (strlen($password) < 6) $errors[] = 'Your password must have at least 6+ characters.';
if ($password !== $confirm) $errors[] = 'Your passwords do not align. Please try again.';

if ($errors) {
  http_response_code(400);
  ob_clean();
  echo json_encode(['success'=>false, 'error'=>implode(' ', $errors)]);
  exit;
}

try {
  // unique email
  $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
  $check->execute([$email]);
  if ($check->fetch()) {
    http_response_code(409);
    ob_clean();
    echo json_encode(['success'=>false,'error'=>'This email is already registered.']);
    exit;
  }

    // was: INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins  = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $ins->execute([$username, $email, $hash]);


  $_SESSION['user_id']  = (int)$pdo->lastInsertId();
  $_SESSION['username'] = $username;

  ob_clean();
  echo json_encode(['success'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  ob_clean();
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
