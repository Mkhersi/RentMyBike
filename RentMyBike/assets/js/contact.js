// assets/js/contact.js
console.log('🟢 contact.js loaded');
document.addEventListener('DOMContentLoaded', () => {
  const form    = document.getElementById('contactForm');
  const message = document.getElementById('contactMessage');

  form.addEventListener('submit', async e => {
    console.log('🟢 contactForm submit handler firing');

    e.preventDefault();
    message.textContent = '';
    message.className = 'error';

    const data = new FormData(form);

    try {
      const res  = await fetch('/api/contact.php', {
        method: 'POST',
        body: data
      });
      const json = await res.json();

      if (json.success) {
        message.className = 'success';
        message.textContent = 'Thanks for reaching out! Your message will reach us soon.';
        form.reset();
      } else {
        message.textContent = json.error || 'We were unable to send your message. Please try again.';
      }
    } catch (err) {
      message.textContent = 'Network error. Please check your connection and retry';
    }
  });
});
