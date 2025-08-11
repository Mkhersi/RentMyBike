<?php
// views/contact.php
include_once __DIR__ . '/_header.php';
?>

<section class="static-page">
  <h1>Contact Us</h1>

  <div id="contactMessage" class="error" style="margin-bottom:1em;"></div>

  <form id="contactForm">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required />

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required />

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="5" required></textarea>

    <button type="submit">Send</button>
  </form>
</section>

<!-- Load the JS that submits to the API -->
<script src="/assets/js/contact.js"></script>

<?php include_once __DIR__ . '/_footer.php'; ?>
