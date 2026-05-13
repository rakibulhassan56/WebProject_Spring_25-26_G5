(() => {
  const patchBase = window.__QUESTION_PATCH__;
  const deleteBase = window.__QUESTION_DELETE__;
  if (!patchBase || !deleteBase) return;

  function cells(tr) {
    const tds = tr.querySelectorAll('td');
    return { textCell: tds[1], optCell: tds[2] };
  }

  function enterEdit(tr) {
    const qid = parseInt(tr.dataset.questionId || '0', 10);
    const { textCell, optCell } = cells(tr);
    if (!textCell || !optCell) return;
    tr.dataset.snapText = textCell.innerHTML;
    tr.dataset.snapOpt = optCell.innerHTML;

    const textPlain = textCell.innerText.trim();
    const lis = Array.from(optCell.querySelectorAll('.opt-list li'));
    const radios = lis
      .map((li) => {
        const oid = li.dataset.optionId;
        const checked = li.classList.contains('correct') ? 'checked' : '';
        const val = li.textContent.trim().replace(/"/g, '&quot;');
        return `<label class="option"><input type="radio" name="correct_${qid}" value="${oid}" ${checked}/> <input type="text" data-opt-id="${oid}" value="${val}"/></label>`;
      })
      .join('');

    textCell.innerHTML = `<textarea class="q-edit-text" rows="3"></textarea>`;
    const ta = textCell.querySelector('textarea');
    if (ta) ta.value = textPlain;
    optCell.innerHTML = `<div class="opt-edit">${radios}</div>`;

    tr.querySelector('.js-edit-q')?.classList.add('hidden');
    tr.querySelector('.js-save-q')?.classList.remove('hidden');
    tr.querySelector('.js-cancel-q')?.classList.remove('hidden');
  }

  function cancelEdit(tr) {
    const { textCell, optCell } = cells(tr);
    if (textCell && tr.dataset.snapText !== undefined) textCell.innerHTML = tr.dataset.snapText;
    if (optCell && tr.dataset.snapOpt !== undefined) optCell.innerHTML = tr.dataset.snapOpt;
    tr.querySelector('.js-edit-q')?.classList.remove('hidden');
    tr.querySelector('.js-save-q')?.classList.add('hidden');
    tr.querySelector('.js-cancel-q')?.classList.add('hidden');
  }

  function renderQuestionRow(tr, q) {
    const { textCell, optCell } = cells(tr);
    if (textCell) {
      const esc = (s) =>
        String(s)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;');
      const nl = esc(q.question_text).replace(/\n/g, '<br>');
      textCell.innerHTML = nl;
    }
    if (optCell) {
      const ul = document.createElement('ul');
      ul.className = 'opt-list';
      (q.options || []).forEach((o) => {
        const li = document.createElement('li');
        li.dataset.optionId = String(o.id);
        if (parseInt(String(o.is_correct), 10) === 1) li.classList.add('correct');
        li.textContent = o.option_text;
        ul.appendChild(li);
      });
      optCell.innerHTML = '';
      optCell.appendChild(ul);
    }
    tr.querySelector('.js-edit-q')?.classList.remove('hidden');
    tr.querySelector('.js-save-q')?.classList.add('hidden');
    tr.querySelector('.js-cancel-q')?.classList.add('hidden');
  }

  async function saveEdit(tr) {
    const qid = parseInt(tr.dataset.questionId || '0', 10);
    const text = tr.querySelector('.q-edit-text')?.value?.trim() || '';
    const correct = tr.querySelector(`input[name="correct_${qid}"]:checked`);
    const correctId = parseInt(correct?.value || '0', 10);
    const options = {};
    tr.querySelectorAll('input[data-opt-id]').forEach((inp) => {
      const id = parseInt(inp.getAttribute('data-opt-id') || '0', 10);
      options[id] = inp.value;
    });
    try {
      const res = await fetch(`${patchBase}&id=${encodeURIComponent(String(qid))}`, {
        method: 'PATCH',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ question_text: text, correct_option_id: correctId, options }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        alert(data.error || 'Update failed');
        return;
      }
      renderQuestionRow(tr, data.question);
    } catch {
      alert('Network error');
    }
  }

  document.getElementById('questions-table')?.addEventListener('click', (e) => {
    const t = e.target;
    if (!(t instanceof HTMLElement)) return;
    const tr = t.closest('tr');
    if (!tr) return;
    if (t.classList.contains('js-edit-q')) enterEdit(tr);
    if (t.classList.contains('js-cancel-q')) cancelEdit(tr);
    if (t.classList.contains('js-save-q')) void saveEdit(tr);
    if (t.classList.contains('js-delete-q')) {
      const qid = parseInt(tr.dataset.questionId || '0', 10);
      if (!confirm('Delete this question?')) return;
      void (async () => {
        try {
          const res = await fetch(`${deleteBase}&id=${encodeURIComponent(String(qid))}`, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data.ok) {
            alert(data.error || 'Delete failed');
            return;
          }
          tr.remove();
        } catch {
          alert('Network error');
        }
      })();
    }
  });
})();
