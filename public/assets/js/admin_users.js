(() => {
  const url = window.__USER_TOGGLE_URL__;
  document.querySelectorAll('.js-toggle-user').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const tr = btn.closest('tr');
      const id = parseInt(tr?.dataset.userId || '0', 10);
      if (!id) return;
      try {
        const res = await fetch(`${url}`, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ user_id: id }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
          alert(data.error || 'Request failed');
          return;
        }
        btn.textContent = data.label;
        btn.dataset.active = String(data.is_active);
        const statusCell = tr.querySelector('.status-cell');
        if (statusCell) statusCell.textContent = data.is_active === 1 ? 'Active' : 'Suspended';
      } catch {
        alert('Network error');
      }
    });
  });
})();
