// assets/js/register.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('regForm');
  const err  = document.getElementById('regError');
  const ok   = document.getElementById('regSuccess');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    err.textContent = ''; ok.style.display = 'none';

    const fd = new FormData(form);
    try {
      const res  = await fetch('/api/auth/register.php', { method:'POST', body: fd });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.error || 'We failed to register you. Please check your details');

      ok.style.display = 'block';
      setTimeout(() => { window.location.href = 'dashboard.php'; }, 700);
    } catch (ex) {
      err.textContent = ex.message;
    }
  });
});
