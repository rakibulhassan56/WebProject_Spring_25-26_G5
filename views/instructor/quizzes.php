<?php
/** @var array $quizzes */
$extraHead = '<script src="/assets/js/instructor_quizzes.js" defer></script>';
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>My quizzes</h1>
  <p><a class="button" href="<?= htmlspecialchars(url('instructor/quizzes/new'), ENT_QUOTES, 'UTF-8') ?>">Create quiz</a></p>
  <table class="data" id="quiz-table">
    <thead>
      <tr><th>Title</th><th>Marks</th><th>Questions</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($quizzes as $q): ?>
      <tr data-quiz-id="<?= (int) $q['id'] ?>">
        <td><?= htmlspecialchars((string) $q['title'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= (int) $q['total_marks'] ?></td>
        <td><?= (int) ($q['question_count'] ?? 0) ?></td>
        <td><span class="badge status-badge"><?= htmlspecialchars((string) $q['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
        <td class="actions">
          <a href="<?= htmlspecialchars(url('instructor/questions', ['quiz_id' => (int) $q['id']]), ENT_QUOTES, 'UTF-8') ?>">Questions</a>
          <a href="<?= htmlspecialchars(url('instructor/quizzes/edit', ['id' => (int) $q['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
          <button type="button" class="js-toggle-publish" data-id="<?= (int) $q['id'] ?>">
            <?= $q['status'] === 'published' ? 'Unpublish' : 'Publish' ?>
          </button>
          <form method="post" action="<?= htmlspecialchars(url('instructor/quizzes/delete'), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this quiz?');" style="display:inline">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= (int) $q['id'] ?>">
            <button type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <script>window.__QUIZ_TOGGLE_URL__ = <?= json_encode(url('api/quizzes/toggle'), JSON_THROW_ON_ERROR) ?>;</script>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
