<?php include_once __DIR__ . '/_header.php'; ?>
<main class="form-page">
  <h1>Log in</h1>
  <form id="loginForm" novalidate>
    <div id="loginError" class="error"></div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />

    <div class="actions">
      <button type="submit" class="btn">Login</button>
      <a class="btn-secondary" href="register.php">Create a new account</a>
    </div>
  </form>
</main>
<script src="/assets/js/login.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
