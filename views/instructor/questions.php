<?php
/** @var array $quiz */
/** @var array $questions */
/** @var array $errors */
/** @var array|null $old */
$extraHead = '<script src="/assets/js/instructor_questions.js" defer></script>';
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Questions — <?= htmlspecialchars((string) $quiz['title'], ENT_QUOTES, 'UTF-8') ?></h1>
  <p><a href="<?= htmlspecialchars(url('instructor/quizzes'), ENT_QUOTES, 'UTF-8') ?>">Back to quizzes</a></p>

  <h2>Add question</h2>
  <form method="post" action="<?= htmlspecialchars(url('instructor/questions/add'), ENT_QUOTES, 'UTF-8') ?>" class="stack card">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">
    <label>Question text
      <textarea name="question_text" rows="3" required><?= htmlspecialchars((string) ($old['question_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
      <?php if (!empty($errors['question_text'])): ?><span class="error"><?= htmlspecialchars($errors['question_text'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Marks
      <input type="number" name="marks" min="1" value="<?= htmlspecialchars((string) ($old['marks'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
      <?php if (!empty($errors['marks'])): ?><span class="error"><?= htmlspecialchars($errors['marks'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <?php for ($i = 0; $i < 4; $i++): ?>
      <label>Option <?= chr(65 + $i) ?>
        <input name="opt<?= $i ?>" value="<?= htmlspecialchars((string) ($old['opt' . $i] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errors['opt' . $i])): ?><span class="error"><?= htmlspecialchars($errors['opt' . $i], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
      </label>
    <?php endfor; ?>
    <fieldset>
      <legend>Correct answer</legend>
      <?php if (!empty($errors['correct_index'])): ?><p class="error"><?= htmlspecialchars($errors['correct_index'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
      <?php for ($i = 0; $i < 4; $i++): ?>
        <label><input type="radio" name="correct_index" value="<?= $i ?>" <?= ((string)($old['correct_index'] ?? '')) === (string)$i ? 'checked' : '' ?> required> Option <?= chr(65 + $i) ?></label>
      <?php endfor; ?>
    </fieldset>
    <button type="submit">Add question</button>
  </form>

  <h2>Existing questions</h2>
  <table class="data" id="questions-table">
    <thead><tr><th>#</th><th>Question</th><th>Options</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($questions as $idx => $qu): ?>
      <tr data-question-id="<?= (int) $qu['id'] ?>">
        <td><?= $idx + 1 ?></td>
        <td class="q-text"><?= nl2br(htmlspecialchars((string) $qu['question_text'], ENT_QUOTES, 'UTF-8')) ?></td>
        <td>
          <ul class="opt-list">
            <?php foreach ($qu['options'] as $o): ?>
              <li data-option-id="<?= (int) $o['id'] ?>" class="<?= (int) $o['is_correct'] === 1 ? 'correct' : '' ?>">
                <?= htmlspecialchars((string) $o['option_text'], ENT_QUOTES, 'UTF-8') ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </td>
        <td>
          <button type="button" class="js-edit-q">Edit</button>
          <button type="button" class="js-save-q hidden">Save</button>
          <button type="button" class="js-cancel-q hidden">Cancel</button>
          <button type="button" class="js-delete-q">Delete</button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <script>
    window.__QUESTION_PATCH__ = <?= json_encode(url('api/questions'), JSON_THROW_ON_ERROR) ?>;
    window.__QUESTION_DELETE__ = <?= json_encode(url('api/questions'), JSON_THROW_ON_ERROR) ?>;
  </script>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
