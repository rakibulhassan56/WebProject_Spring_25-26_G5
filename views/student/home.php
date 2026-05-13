<?php
/** @var array $stats */
/** @var array $quizzes */
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Student dashboard</h1>
  <section class="widgets">
    <div class="widget"><h3>Published quizzes</h3><p class="big"><?= (int) $stats['published_quizzes'] ?></p></div>
    <div class="widget"><h3>Attempts taken</h3><p class="big"><?= (int) $stats['attempts_taken'] ?></p></div>
    <div class="widget"><h3>Total score earned</h3><p class="big"><?= (int) $stats['total_score'] ?></p></div>
  </section>

  <h2>Available quizzes</h2>
  <table class="data">
    <thead><tr><th>Title</th><th>Marks</th><th>Time</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($quizzes as $q): ?>
      <tr class="<?= !empty($q['attempted']) ? 'muted' : '' ?>">
        <td><?= htmlspecialchars((string) $q['title'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= (int) $q['total_marks'] ?></td>
        <td><?= (int) $q['time_limit_minutes'] ?> min</td>
        <td>
          <?php if (!empty($q['attempted'])): ?>
            <span class="badge">Score: <?= (int) ($q['last_score'] ?? 0) ?></span>
          <?php else: ?>
            <form method="post" action="<?= htmlspecialchars(url('student/quiz/start'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="quiz_id" value="<?= (int) $q['id'] ?>">
              <button type="submit">Start Quiz</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
