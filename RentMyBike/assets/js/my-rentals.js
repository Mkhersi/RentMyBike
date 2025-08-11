// assets/js/my-rentals.js
document.addEventListener('DOMContentLoaded', async () => {
  const upC = document.getElementById('upcoming');
  const hist = document.getElementById('history');

  try {
    const res = await fetch('../api/rentals/list.php');
    const json = await res.json();
    if (!json.success) throw new Error(json.error);

    const renderTable = (data, container, canCancel) => {
      if (!data.length) {
        container.innerHTML = `<p>${canCancel ? 'You have no upcoming rentals.' : 'You have no past rentals.'}</p>`;
        return;
      }
      const rows = data.map(r => `
        <tr>
          <td><img src="../assets/images/${r.image_url}" style="width:60px"> ${r.make} ${r.model}</td>
          <td>${r.start_date}</td>
          <td>${r.end_date}</td>
          <td>£${parseFloat(r.total_rate).toFixed(2)}</td>
          <td>
            ${canCancel 
              ? `<button data-id="${r.rental_id}">Cancel</button>` 
              : ''}
          </td>
        </tr>
      `).join('');
      container.innerHTML = `
        <table>
          <thead><tr><th>Bike</th><th>Start</th><th>End</th><th>Total</th><th>Action</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>`;
      if (canCancel) {
        container.querySelectorAll('button[data-id]').forEach(btn => {
          btn.addEventListener('click', async () => {
            const ok = await RMB.confirm('Do you want to cancel this booking?');
            if (!ok) return;
            try {
              const resp = await fetch(`../api/rentals/cancel.php?id=${btn.dataset.id}`);
              const jr   = await resp.json();
              if (jr.success) btn.closest('tr').remove();
              else alert(jr.error);
            } catch {
              alert('Network error.');
            }
          });
        });
      }
    };

    renderTable(json.upcoming, upC, true);
    renderTable(json.past, hist, false);

  } catch (e) {
    upC.innerHTML   = `<p class="error">${e.message}</p>`;
    hist.innerHTML  = `<p class="error">${e.message}</p>`;
  }
});
