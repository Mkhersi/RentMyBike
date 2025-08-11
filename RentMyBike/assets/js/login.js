document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const err  = document.getElementById('loginError');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    err.textContent = '';

    const fd = new FormData(form);
    try {
      const res  = await fetch('/api/auth/login.php', { method:'POST', body: fd });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.error || 'We were not able to log you in successfully. Please check your details and try again');

      window.location.href = 'dashboard.php';
    } catch (ex) {
      err.textContent = ex.message;
    }
  });
});
