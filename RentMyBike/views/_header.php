<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$current    = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RentMyBike.io</title>
  <link rel="icon" href="/assets/images/favicon.ico" />
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
<header>
  <nav>
    <a href="index.php" class="logo">RentMyBike.io</a>

    <?php if ($current === 'index.php'): ?>
      <form id="headerSearchForm" class="header-search" role="search" aria-label="Search bikes">
        <input id="headerSearchInput" name="q" type="text" placeholder="Search by make or model…" aria-label="Search by make or model" />
        <button type="submit" class="btn">Search</button>
      </form>
    <?php endif; ?>

    <ul class="nav-links">
      <li><a href="index.php"  class="<?= $current==='index.php'  ? 'active' : '' ?>">Home</a></li>
      <li><a href="about.php"  class="<?= $current==='about.php'  ? 'active' : '' ?>">About</a></li>
      <li><a href="contact.php"class="<?= $current==='contact.php'? 'active' : '' ?>">Contact</a></li>
    </ul>

    <div class="auth-links">
      <?php if ($isLoggedIn): ?>
        <a href="dashboard.php">Dashboard (<?= htmlspecialchars($username) ?>)</a>
        <a href="../logout.php">Logout</a>
      <?php else: ?>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
<main>
