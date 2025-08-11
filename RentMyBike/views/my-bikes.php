<?php
// views/my-bikes.php
include_once __DIR__ . '/_header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<section class="my-bikes">
  <h1>My Bikes</h1>
  <div id="myBikesList"><p>Loading your bikes…</p></div>
</section>

<script src="../assets/js/my-bikes.js"></script>

<?php include_once __DIR__ . '/_footer.php'; ?>
