// assets/js/my-bikes.js
document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('myBikesList');

  const renderEmpty = () => {
    container.innerHTML = '<p>You have not listed a bike yet. <a href="add-bike.php">Want to add one now?</a>.</p>';
  };

  try {
    const res = await fetch('../api/bikes/list-by-user.php', { credentials: 'include' });
    const payload = await res.json();

    if (!payload.success) {
      container.innerHTML = `<p class="error">${payload.error || 'We couldnt load your bikes. Please reload the page and try again.'}</p>`;
      return;
    }

    const bikes = payload.data || [];
    if (!bikes.length) {
      renderEmpty();
      return;
    }

    container.innerHTML = bikes.map(b => `
      <div class="bike-card" data-id="${b.bike_id}">
        <img src="../assets/images/${b.image_url}" alt="${b.make} ${b.model}" />
        <h2>${b.make} ${b.model}</h2>
        <p>£${parseFloat(b.rental_rate).toFixed(2)} / day</p>
        <div class="actions">
          <a href="bike-details.php?id=${b.bike_id}" class="btn">View</a>
          <a href="edit-bike.php?id=${b.bike_id}" class="btn">Edit</a>
          <button data-id="${b.bike_id}" class="btn delete">Delete</button>
        </div>
      </div>
    `).join('');

    // Single delegated listener for delete buttons
    container.addEventListener('click', async (e) => {
      const btn = e.target.closest('.delete');
      if (!btn) return;

      const id = btn.dataset.id;
      if (!id) return;

      // If you don’t have RMB.confirm, use window.confirm
      const ok = (window.RMB && RMB.confirm) ? await RMB.confirm('Delete this bike?') : confirm('Are you sure you want to delete this bike?');
      if (!ok) return;

      try {
        const body = new URLSearchParams();
        body.set('bike_id', id);

        const resp = await fetch('../api/bikes/delete.php', {
           method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `bike_id=${encodeURIComponent(id)}`,
          credentials: 'include'
      });


        const json = await resp.json();
        if (json.success) {
          // Remove card from DOM
          const card = btn.closest('.bike-card');
          if (card) card.remove();

          // If list becomes empty, show empty state
          if (!container.querySelector('.bike-card')) {
            renderEmpty();
          }
        } else {
          alert(json.error || 'We could not delete your bike. Please retry.');
        }
      } catch {
        alert('Network error. Please try again.');
      }
    });
  } catch {
    container.innerHTML = '<p class="error">Error loading bikes.</p>';
  }
});
