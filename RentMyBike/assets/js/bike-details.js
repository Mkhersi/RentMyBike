// assets/js/bike-details.js
document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('bikeDetails');
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');

  if (!id) {
    container.innerHTML = '<p class="error">You did not specify a bike.</p>';
    return;
  }

  try {
    const res = await fetch(`../api/bikes/details.php?id=${id}`);
    const payload = await res.json();

    if (!payload.success) {
      container.innerHTML = `<p class="error">${payload.error || 'We could not load your bike. Please retry.'}</p>`;
      return;
    }

    const b = payload.data;
    container.innerHTML = `
      <h1>${b.make} ${b.model}</h1>
      <img src="../assets/images/${b.image_url}" alt="${b.make} ${b.model}" />
      <div class="specs">
        <div><strong>Type:</strong> ${b.bike_type}</div>
        <div><strong>Frame Size:</strong> ${b.frame_size}</div>
        <div><strong>Gears:</strong> ${b.gear_count}</div>
        <div><strong>Year:</strong> ${b.year}</div>
        <div><strong>Location:</strong> ${b.location}</div>
        <div><strong>Condition:</strong> ${b.condition}</div>
        <div><strong>Rate:</strong> £${parseFloat(b.rental_rate).toFixed(2)} / day</div>
        <div><strong>Owner:</strong> ${b.owner_name}</div>
      </div>
      <div class="actions">
        <button class="book" id="bookBtn">Book Now</button>
        <a href="edit-bike.php?id=${b.bike_id}" class="edit">Edit</a>
        <button class="delete" id="deleteBtn">Delete</button>
      </div>
    `;

   document.getElementById('bookBtn').addEventListener('click', () => {
  // Redirect to the booking page with the bike ID in the URL
  window.location.href = `book-bike.php?id=${b.bike_id}`;
});


    // replace the whole deleteBtn listener with this
document.getElementById('deleteBtn').addEventListener('click', async () => {
  const ok = (window.RMB && RMB.confirm)
    ? await RMB.confirm('Delete this bike?')
    : confirm('Are you sure you want to delete this bike?');

  if (!ok) return;

  try {
    const body = new URLSearchParams();
    body.set('bike_id', String(b.bike_id));

    const res = await fetch('../api/bikes/delete.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body
    });

    const json = await res.json();
    if (json.success) {
      // send them somewhere sensible after delete
      window.location.href = 'my-bikes.php'; // or 'dashboard.php' if you prefer
    } else {
      alert(json.error || 'There was an error deleting your bike. Please retry.');
    }
  } catch {
    alert('There was a network error. Please retry.');
  }
});


  } catch (err) {
    console.error(err);
    container.innerHTML = '<p class="error">Unexpected error loading bike. Please retry</p>';
  }
});
