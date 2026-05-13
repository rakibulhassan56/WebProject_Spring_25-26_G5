(() => {
  const root = document.getElementById('quiz-root');
  const form = document.getElementById('quiz-form');
  const banner = document.getElementById('timeup-banner');
  const timerEl = document.getElementById('timer-display');
  const msg = document.getElementById('quiz-msg');
  if (!root || !form || !timerEl) return;

  let submitting = false;

  const minutes = parseInt(String(root.dataset.minutes || '0'), 10);
  let seconds = Math.max(0, minutes * 60);
  let fired = false;

  function fmt(s) {
    const m = Math.floor(s / 60);
    const r = s % 60;
    return `${String(m).padStart(2, '0')}:${String(r).padStart(2, '0')}`;
  }

  function tick() {
    timerEl.textContent = fmt(seconds);
    if (seconds <= 0) {
      if (!fired) {
        fired = true;
        banner?.classList.remove('hidden');
        const btn = document.getElementById('submit-quiz');
        btn?.click();
      }
      return;
    }
    seconds -= 1;
  }

  tick();
  setInterval(tick, 1000);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (submitting) return;
    submitting = true;
    msg?.classList.add('hidden');
    const attemptId = parseInt(String(new FormData(form).get('attempt_id') || '0'), 10);
    const answers = {};
    form.querySelectorAll('input[type="radio"]:checked').forEach((r) => {
      const name = r.getAttribute('name') || '';
      const m = /^q_(\d+)$/.exec(name);
      if (m) answers[parseInt(m[1], 10)] = parseInt(String(r.value), 10);
    });
    const submitUrl = form.getAttribute('data-submit-url');
    if (!submitUrl) return;
    try {
      const res = await fetch(submitUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ attempt_id: attemptId, answers }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        msg.textContent = data.error || 'Could not submit quiz.';
        msg.classList.remove('hidden');
        submitting = false;
        return;
      }
      if (data.redirect) {
        window.location.href = data.redirect;
      }
    } catch {
      msg.textContent = 'Network error.';
      msg.classList.remove('hidden');
      submitting = false;
    }
  });
})();
