(() => {
  const url = window.__LEADERBOARD_URL__;
  const tbody = document.querySelector('#leaderboard-table tbody');
  const cd = document.getElementById('lb-countdown');
  if (!url || !tbody) return;

  let remain = 30;

  function render(rows) {
    tbody.innerHTML = '';
    rows.forEach((r, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${idx + 1}</td><td>${escapeHtml(r.name)}</td><td>${Number(r.cumulative)}</td>`;
      tbody.appendChild(tr);
    });
    if (!rows.length) {
      const tr = document.createElement('tr');
      tr.innerHTML = '<td colspan="3">No data yet.</td>';
      tbody.appendChild(tr);
    }
  }

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  async function load() {
    try {
      const res = await fetch(url, { headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (data.ok) render(data.rows || []);
    } catch {
      /* ignore */
    }
    remain = 30;
  }

  void load();
  setInterval(() => {
    remain -= 1;
    if (cd) cd.textContent = `Refreshing in ${Math.max(0, remain)}s`;
    if (remain <= 0) void load();
  }, 1000);
})();
