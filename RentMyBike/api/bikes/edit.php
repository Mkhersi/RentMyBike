<?php
// api/bikes/edit.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();
require __DIR__ . '/../../config/db.php';

function json_exit(int $status, array $data) {
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_SLASHES);
  exit;
}

if (empty($_SESSION['user_id'])) {
  json_exit(401, ['ok'=>false,'error'=>'Please log in to continue.']);
}
$userId = (int)$_SESSION['user_id'];

// Inputs
$bikeId      = isset($_POST['bike_id']) ? (int)$_POST['bike_id'] : 0;
$make        = trim($_POST['make'] ?? '');
$model       = trim($_POST['model'] ?? '');
$bikeType    = trim($_POST['bike_type'] ?? '');
$frameSize   = trim($_POST['frame_size'] ?? '');
$gearCount   = isset($_POST['gear_count']) ? (int)$_POST['gear_count'] : null;
$year        = isset($_POST['year']) ? (int)$_POST['year'] : null;
$location    = trim($_POST['location'] ?? '');
$rentalRate  = isset($_POST['rental_rate']) ? (float)$_POST['rental_rate'] : null;
$cond        = trim($_POST['condition'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($bikeId<=0) json_exit(400,['ok'=>false,'error'=>'You have not selected a bike yet. Please select one to continue.']);

$missing = [];
foreach (['make'=>$make,'model'=>$model,'bike_type'=>$bikeType,'frame_size'=>$frameSize,'location'=>$location] as $k=>$v) {
  if ($v==='') $missing[]=$k;
}
if ($missing) json_exit(422,['ok'=>false,'error'=>'MISSING_FIELDS','fields'=>$missing]);
if ($rentalRate!==null && $rentalRate<0) json_exit(422,['ok'=>false,'error'=>'The rental rate submitted is invalid. Please try again.']);
if ($year!==null && ($year<1950 || $year>(int)date('Y')+1)) json_exit(422,['ok'=>false,'error'=>'The year submitted is invalid. Please retry.']);
if ($gearCount!==null && $gearCount<0) json_exit(422,['ok'=>false,'error'=>'The gear count submitted is invalid. Please try again.']);

// Fetch bike + ownership
$stmt = $pdo->prepare("SELECT bike_id, user_id, image_url FROM bikes WHERE bike_id=:id LIMIT 1");
$stmt->execute([':id'=>$bikeId]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$current) json_exit(404,['ok'=>false,'error'=>'We could not find that bike. Please try again.']);
if ((int)$current['user_id'] !== $userId) json_exit(403,['ok'=>false,'error'=>'Error. This is not your bike.']);
$currentImage = $current['image_url'] ?? null;

// Optional image upload — keep same convention as add.php:
//   - save file under /assets/images/
//   - store ONLY the filename in DB
$newFilename = null;
if (!empty($_FILES['image']['name'])) {
  $file = $_FILES['image'];
  if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    json_exit(400,['ok'=>false,'error'=>'There was a problem uploading your image. Please retry.','code'=>$file['error']]);
  }
  if ($file['size'] > 2*1024*1024) { // match add.php 2MB
    json_exit(413,['ok'=>false,'error'=>'Your image is too large. Please stick to uploading images >=2MB']);
  }
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime  = $finfo->file($file['tmp_name']);
  $ext = match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif', default=>null };
  if (!$ext) json_exit(415,['ok'=>false,'error'=>'We do not support this image type. Please stick to jpg, png, gif.']);

  $uploadDir = __DIR__ . '/../../assets/images';
  if (!is_dir($uploadDir)) @mkdir($uploadDir,0755,true);

  $safeBase = preg_replace('/[^a-zA-Z0-9_-]/','-', strtolower($make.'-'.$model));
  $newFilename = time() . '_' . $safeBase . '-' . $bikeId . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
  $destAbs = $uploadDir . '/' . $newFilename;
  if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
    json_exit(500,['ok'=>false,'error'=>'There was an error saving your image. Please retry.']);
  }
}

// Build update
$fields = [
  'make'        => $make,
  'model'       => $model,
  'bike_type'   => $bikeType,
  'frame_size'  => $frameSize,
  'gear_count'  => $gearCount,
  'year'        => $year,
  'location'    => $location,
  'rental_rate' => $rentalRate,
  'condition'   => $cond,
];
if ($newFilename !== null) {
  $fields['image_url'] = $newFilename; // store filename only
}

$set = [];
$params = [':id'=>$bikeId, ':uid'=>$userId];
foreach ($fields as $col=>$val) { $set[]="`$col`=:$col"; $params[":$col"]=$val; }

$sql = "UPDATE bikes SET ".implode(', ',$set).", updated_at=NOW()
        WHERE bike_id=:id AND user_id=:uid LIMIT 1";
$ok = $pdo->prepare($sql)->execute($params);
if (!$ok) json_exit(500,['ok'=>false,'error'=>'We could not update the bike. Please retry.']);

// Clean up old image if replaced
if ($newFilename !== null && $currentImage && $currentImage !== $newFilename) {
  $oldAbs = __DIR__ . '/../../assets/images/' . $currentImage;
  if (is_file($oldAbs)) @unlink($oldAbs);
}

json_exit(200,['ok'=>true,'bike_id'=>$bikeId,'image_url'=>$newFilename ?? $currentImage]);
