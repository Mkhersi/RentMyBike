<?php
// views/my-rentals.php
include_once __DIR__ . '/_header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<section class="my-rentals">
  <h1>My Rentals</h1>

  <h2>Future/upcoming</h2>
  <div id="upcoming"><p>Loading…</p></div>

  <h2>Past</h2>
  <div id="history"><p>Loading…</p></div>
</section>
<script src="../assets/js/my-rentals.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
