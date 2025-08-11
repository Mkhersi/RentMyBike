<?php
// api/bikes/add.php
header('Content-Type: application/json');
session_start();
require __DIR__ . '/../../config/db.php';

// 1. Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Please log in to continue']);
    exit;
}

// 2. Validate POST fields
$fields = ['make','model','bike_type','frame_size','gear_count','year','location','rental_rate','condition'];
$data = [];
foreach ($fields as $f) {
    if (empty($_POST[$f])) {
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>"'{$f}' is needed to proceed."]);
        exit;
    }
    $data[$f] = trim($_POST[$f]);
}

// 3. Validate numeric fields
if (!ctype_digit($data['gear_count']) || !ctype_digit($data['year'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'The gear count and year fields must be submitted as integers. Please try again']);
    exit;
}
if (!is_numeric($data['rental_rate'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'The rental rate field must be a number. Please try again']);
    exit;
}

// 4. Handle image upload
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'There was an error uploading your image. Please try again']);
    exit;
}
$img     = $_FILES['image'];
// Strong MIME check
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($img['tmp_name']);

$mimeMap = [
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/gif'  => 'gif',
  'image/webp' => 'webp',
];

if (!isset($mimeMap[$mime])) {
    http_response_code(415);
    echo json_encode(['success'=>false,'error'=>'We only support the following file types: JPG, PNG, GIF and WEBP. Please re-upload your image in one of these']);
    exit;
}

// Force extension from MIME type
$ext = $mimeMap[$mime];

if ($img['size'] > 2*1024*1024) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Your image is too large. We only accept max 2MB. Please re-upload your image within that parameter.']);
    exit;
}

// ensure images folder exists
$targetDir = __DIR__ . '/../../assets/images/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}
$filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest     = $targetDir . $filename;
if (!move_uploaded_file($img['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'There was an error saving your image. Please try again']);
    exit;
}

// 5. Insert record


try {
    // Define $sql here—this must come before prepare()
    $sql = "INSERT INTO bikes
      (user_id, make, model, bike_type, frame_size, gear_count, year, location, rental_rate, `condition`, image_url)
     VALUES
      (:uid, :make, :model, :type, :size, :gears, :year, :loc, :rate, :cond, :img)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      'uid'   => $_SESSION['user_id'],
      'make'  => $data['make'],
      'model' => $data['model'],
      'type'  => $data['bike_type'],
      'size'  => $data['frame_size'],
      'gears' => $data['gear_count'],
      'year'  => $data['year'],
      'loc'   => $data['location'],
      'rate'  => $data['rental_rate'],
      'cond'  => $data['condition'],
      'img'   => $filename
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      // you can drop the debug once it works
      'error'   => 'DB Error: ' . $e->getMessage()
    ]);
}
