<?php include_once __DIR__ . '/_header.php'; ?>
<main class="form-page">
  <h1>Create new account</h1>
  <form id="regForm" novalidate>
    <div id="regError" class="error"></div>

    <label for="username">Username <small class="badge">Required</small></label>
    <input type="text" id="username" name="username" required />

    <label for="email">Email <small class="badge">Required</small></label>
    <input type="email" id="email" name="email" required />

    <label for="password">Password <small class="badge">6+ chars</small></label>
    <input type="password" id="password" name="password" required minlength="6"/>

    <label for="confirm">Confirm Password</label>
    <input type="password" id="confirm" name="confirm" required minlength="6"/>

    <div class="actions">
      <button type="submit" class="btn">Register</button>
      <a class="btn-secondary" href="login.php">I already have an account</a>
    </div>

    <div id="regSuccess" class="success" style="display:none;">Account created—redirecting…</div>
  </form>
</main>
<script src="/assets/js/register.js"></script>
<?php include_once __DIR__ . '/_footer.php'; ?>
