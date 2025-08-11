<?php
// views/edit-bike.php
include_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../config/db.php'; // adjust if your db.php is elsewhere

if (!isset($_SESSION['user_id'])) {
    header('Location: /views/login.php');
    exit;
}

$bikeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($bikeId <= 0) {
    header('Location: /views/dashboard.php');
    exit;
}

// Fetch bike and confirm ownership
$stmt = $pdo->prepare("SELECT * FROM bikes WHERE bike_id = :id LIMIT 1");
$stmt->execute([':id' => $bikeId]);
$bike = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bike || (int)$bike['user_id'] !== (int)$_SESSION['user_id']) {
    header('Location: /views/dashboard.php');
    exit;
}
?>
<main class="form-page">
  <h1>Edit Bike</h1>

  <div id="editBikeError" class="error" style="display:none;"></div>

  <form id="editBikeForm" method="post" action="/api/bikes/edit.php" enctype="multipart/form-data">
    <input type="hidden" name="bike_id" value="<?= htmlspecialchars($bike['bike_id']) ?>">

    <label for="make">Make</label>
    <input type="text" id="make" name="make" required
           value="<?= htmlspecialchars($bike['make'] ?? '') ?>">

    <label for="model">Model</label>
    <input type="text" id="model" name="model" required
           value="<?= htmlspecialchars($bike['model'] ?? '') ?>">

    <label for="bike_type">Type</label>
    <input type="text" id="bike_type" name="bike_type" required
           value="<?= htmlspecialchars($bike['bike_type'] ?? '') ?>">

    <label for="frame_size">Frame Size</label>
    <input type="text" id="frame_size" name="frame_size" required
           value="<?= htmlspecialchars($bike['frame_size'] ?? '') ?>">

    <label for="gear_count">Gear Count</label>
    <input type="number" id="gear_count" name="gear_count" min="1" required
           value="<?= htmlspecialchars($bike['gear_count'] ?? '') ?>">

    <label for="year">Year</label>
    <input type="number" id="year" name="year" required
           value="<?= htmlspecialchars($bike['year'] ?? '') ?>">

    <label for="location">Location</label>
    <input type="text" id="location" name="location" required
           value="<?= htmlspecialchars($bike['location'] ?? '') ?>">

    <label for="rental_rate">Daily Rate (£)</label>
    <input type="number" step="0.01" id="rental_rate" name="rental_rate" required
           value="<?= htmlspecialchars($bike['rental_rate'] ?? '') ?>">

    <label for="condition">Condition</label>
    <input type="text" id="condition" name="condition" required
           value="<?= htmlspecialchars($bike['condition'] ?? '') ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($bike['description'] ?? '') ?></textarea>

    <label for="image">Bike Image</label>
    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp">


    <div id="currentImage" style="margin:10px 0;">
      <?php if (!empty($bike['image_url'])): ?>
        <p>Current image:</p>
        <img src="<?= htmlspecialchars($bike['image_url']) ?>">
      <?php else: ?>
        <p>No image uploaded.</p>
      <?php endif; ?>
    </div>

    <button type="submit">Save Changes</button>
  </form>
</main>

<!-- Keep this include at the end so the DOM exists before the script runs -->
<script src="/assets/js/edit-bike.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
