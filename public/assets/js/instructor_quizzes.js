(() => {
  const base = window.__QUIZ_TOGGLE_URL__;
  document.querySelectorAll('.js-toggle-publish').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const id = parseInt(btn.getAttribute('data-id') || '0', 10);
      if (!id || !base) return;
      try {
        const res = await fetch(`${base}&id=${encodeURIComponent(String(id))}`, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { Accept: 'application/json' },
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
          alert(data.error === 'needs_questions' ? 'Add at least one question before publishing.' : (data.error || 'Request failed'));
          return;
        }
        btn.textContent = data.label;
        const tr = btn.closest('tr');
        const badge = tr?.querySelector('.status-badge');
        if (badge) badge.textContent = data.status;
      } catch {
        alert('Network error');
      }
    });
  });
})();
