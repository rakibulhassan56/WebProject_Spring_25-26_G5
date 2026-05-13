<?php
/** @var array|null $quiz */
/** @var array $errors */
require base_path('views/layouts/header.php');
$q = $quiz ?? [];
$isEdit = !empty($q['id']) && (int) $q['id'] > 0;
?>
<main class="container narrow">
  <h1><?= $isEdit ? 'Edit quiz' : 'Create quiz' ?></h1>
  <form method="post" action="<?= htmlspecialchars(url('instructor/quizzes/save'), ENT_QUOTES, 'UTF-8') ?>" class="stack">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= $isEdit ? (int) $q['id'] : 0 ?>">
    <label>Title
      <input name="title" required value="<?= htmlspecialchars((string) ($q['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <?php if (!empty($errors['title'])): ?><span class="error"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Description
      <textarea name="description" rows="4"><?= htmlspecialchars((string) ($q['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
    </label>
    <label>Total marks
      <input type="text" value="<?= $isEdit ? (int) ($q['total_marks'] ?? 0) : '0 (computed from questions)' ?>" readonly>
    </label>
    <label>Time limit (minutes)
      <input type="number" name="time_limit_minutes" min="1" required value="<?= (int) ($q['time_limit_minutes'] ?? 30) ?>">
      <?php if (!empty($errors['time_limit_minutes'])): ?><span class="error"><?= htmlspecialchars($errors['time_limit_minutes'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Status
      <select name="status">
        <?php $st = (string) ($q['status'] ?? 'draft'); ?>
        <option value="draft" <?= $st === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $st === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
      <?php if (!empty($errors['status'])): ?><span class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <button type="submit">Save</button>
  </form>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
