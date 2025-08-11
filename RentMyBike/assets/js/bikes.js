// assets/js/bikes.js
console.log('🟢 bikes.js loaded');

document.addEventListener('DOMContentLoaded', () => {
  const grid   = document.getElementById('bikeGrid');
  const sortEl = document.getElementById('sort');
  const hForm  = document.getElementById('headerSearchForm');
  const hInput = document.getElementById('headerSearchInput');

  function renderSkeletons(count=6){
    grid.innerHTML = `<div class="skel-grid">${
      Array.from({length:count}).map(()=>`
        <div class="skel-card">
          <div class="skeleton skel-thumb"></div>
          <div class="skeleton skel-line"></div>
          <div class="skeleton skel-line small"></div>
        </div>
      `).join('')
    }</div>`;
  }

  async function loadBikes(query = '', sort = 'newest') {
    renderSkeletons(6);
    const url = new URL('/api/bikes/list.php', window.location.origin);
    if (query) url.searchParams.set('q', query);
    if (sort)  url.searchParams.set('sort', sort);

    try {
      const res  = await fetch(url);
      const json = await res.json();
      if (!json.success) throw new Error(json.error || 'There was an error loading the bikes.');

      const data = json.data;
      if (!data.length) { grid.innerHTML = '<p>No bikes found.</p>'; return; }

      grid.innerHTML = data.map(b => `
        <div class="bike-card">
          <img src="/assets/images/${b.image_url || 'bike placeholder.png'}" alt="${b.make} ${b.model}" />
          <h2>${b.make} ${b.model}</h2>
          <p><span class="badge">${b.location || '—'}</span></p>
          <p><strong>£${parseFloat(b.rental_rate).toFixed(2)}</strong> / day</p>
          <a class="btn" href="bike-details.php?id=${b.bike_id}">View Details</a>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<p class="error">${e.message}</p>`;
    }
  }

  // initial load
  loadBikes('', sortEl?.value || 'newest');

  // header search
  hForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    loadBikes(hInput.value.trim(), sortEl.value);
  });

  // sort change
  sortEl?.addEventListener('change', () => {
    loadBikes(hInput?.value.trim() || '', sortEl.value);
  });
});

