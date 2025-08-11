// assets/js/book-bike.js
document.addEventListener('DOMContentLoaded', async () => {
  const info = document.getElementById('bookingInfo');
  const form = document.getElementById('bookingForm');
  const err  = document.getElementById('bookingError');
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  if (!id) {
    info.innerHTML = '<p class="error">You have not selected a bike yet.</p>';
    return;
  }

  try {
    // Load bike details
    let res = await fetch(`../api/bikes/details.php?id=${id}`);
    let { success, data, error } = await res.json();
    if (!success) throw new Error(error);

    info.innerHTML = `
      <h2>${data.make} ${data.model}</h2>
      <img src="../assets/images/${data.image_url}" alt="" style="max-width:300px;display:block;margin-bottom:10px">
      <p>Rate: £${parseFloat(data.rental_rate).toFixed(2)} / day</p>
    `;
    form.style.display = 'block';

    form.addEventListener('submit', async e => {
      e.preventDefault();
      err.textContent = '';
      const fd = new FormData(form);
      fd.append('bike_id', id);
      try {
        res = await fetch('../api/rentals/book.php', { method:'POST', body:fd });
        const json = await res.json();
        if (json.success) {
          window.location.href = 'my-rentals.php';
        } else {
          err.textContent = json.error || 'We could not complete your booking. Please try again.';
        }
      } catch {
        err.textContent = 'Network error. Please check your connection and retry.';
      }
    });
  } catch (e) {
    info.innerHTML = `<p class="error">${e.message}</p>`;
  }
});
