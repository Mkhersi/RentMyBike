<?php
include_once __DIR__ . '/_header.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<section class="welcome-banner">
  <h1>Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Owner') ?>!</h1>
  <p>You can now manage your bookings and bikes below:.</p>
</section>

<section class="dash-cards">
  <div class="card">
    <h3>Your Bikes</h3>
    <p>You can add, edit, or delete your bikes.</p>
    <a class="btn" href="my-bikes.php">Go to My Bikes</a>
    <a class="btn-secondary" href="add-bike.php">Add a Bike</a>
  </div>
  <div class="card">
    <h3>My Rentals</h3>
    <p>You can view your upcoming and past bookings.</p>
    <a class="btn" href="my-rentals.php">View Rentals</a>
  </div>
  <div class="card">
    <h3>Browse</h3>
    <p>You can explore bikes that are available to rent.</p>
    <a class="btn" href="index.php">Browse Bikes</a>
  </div>
</section>
<?php include_once __DIR__ . '/_footer.php'; ?>
