<?php
// views/add-bike.php
include_once __DIR__ . '/_header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<main class="form-page">
  <h1>Add New Bike</h1>
  <form id="addBikeForm" enctype="multipart/form-data">
    <div id="addBikeError" class="error"></div>

    <label for="make">Make</label>
    <input type="text" id="make" name="make" required />

    <label for="model">Model</label>
    <input type="text" id="model" name="model" required />

    <label for="bike_type">Type</label>
    <input type="text" id="bike_type" name="bike_type" required />

    <label for="frame_size">Frame Size</label>
    <input type="text" id="frame_size" name="frame_size" required />

    <label for="gear_count">Gear Count</label>
    <input type="number" id="gear_count" name="gear_count" min="1" required />

    <label for="year">Year</label>
    <input type="number" id="year" name="year" required />

    <label for="location">Location</label>
    <input type="text" id="location" name="location" required />

    <label for="rental_rate">Daily Rate (£)</label>
    <input type="number" step="0.01" id="rental_rate" name="rental_rate" required />

    <label for="condition">Condition</label>
    <input type="text" id="condition" name="condition" required />

    <label for="image">Bike Image</label>
    <input type="file" id="image" name="image" accept="image/*" required />

    <button type="submit">Add Bike</button>
  </form>
</main>

<script src="../assets/js/add-bike.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
