<?php
// views/book-bike.php
include_once __DIR__ . '/_header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<section class="booking">
  <h1>Book a Bike</h1>
  <div id="bookingInfo">
    <p>Loading bike information…</p>
  </div>
  <form id="bookingForm" style="display:none">
    <div id="bookingError" class="error"></div>
    <label for="start_date">Start Date</label>
    <input type="date" id="start_date" name="start_date" required />
    <label for="end_date">End Date</label>
    <input type="date" id="end_date" name="end_date" required />
    <button type="submit">Confirm Booking</button>
  </form>
</section>
<script src="../assets/js/book-bike.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
