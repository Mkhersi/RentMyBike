// assets/js/add-bike.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('addBikeForm');
  const err  = document.getElementById('addBikeError');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    err.textContent = '';

    const data = new FormData(form);

    try {
      const res  = await fetch('../api/bikes/add.php', {
        method: 'POST',
        body: data
      });
      const json = await res.json();

      if (json.success) {
        window.location.href = 'my-bikes.php';
      } else {
        err.textContent = json.error || 'There was an error adding this bike. Please retry';
      }
    } catch {
      err.textContent = 'Network error. Please try again.';
    }
  });
});
