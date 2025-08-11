(function () {
  const form = document.getElementById('editBikeForm');
  if (!form) return;

  const errorBox = document.getElementById('editBikeError');

  function showError(msg) {
    if (!errorBox) { alert(msg); return; }
    errorBox.style.display = 'block';
    errorBox.textContent = msg;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorBox && (errorBox.style.display = 'none');

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        body: fd,
        credentials: 'include'
      });

      let data;
      try {
        data = await res.json();
      } catch {
        showError('Unexpected server response.');
        return;
      }

      if (!res.ok || !data.ok) {
        const code = data && data.error ? data.error : 'UPDATE_FAILED';
        const fields = data && data.fields ? `: ${data.fields.join(', ')}` : '';
        showError(`We were unable to update this bike. Please try again. (${code})${fields}`);
        return;
      }

      // Success: redirect to "My Bikes" (adjust to your route)
      alert('Bike was updated successfully.');
      window.location.href = '/views/my-bikes.php';
    } catch (err) {
      console.error(err);
      showError('Network error. Please check your connection and retry.');
    }
  });
})();
