<?php
/** @var array $attempt */
/** @var bool $passed */
/** @var int $threshold */
/** @var array $breakdown */
require base_path('views/layouts/header.php');
$total = (int) $attempt['total_marks'];
$score = (int) $attempt['score'];
$pct = $total > 0 ? round(100 * $score / $total, 1) : 0;
?>
<main class="container">
  <h1>Result: <?= htmlspecialchars((string) $attempt['quiz_title'], ENT_QUOTES, 'UTF-8') ?></h1>
  <p class="lead">Your score: <strong><?= $score ?></strong> / <?= $total ?> (<?= $pct ?>%)</p>
  <div class="banner <?= $passed ? 'ok' : 'bad' ?>"><?= $passed ? 'Pass' : 'Fail' ?> — pass threshold is 60% (<?= $threshold ?> marks).</div>

  <h2>Breakdown</h2>
  <table class="data">
    <thead>
      <tr><th>Question</th><th>Your answer</th><th>Correct answer</th></tr>
    </thead>
    <tbody>
    <?php foreach ($breakdown as $row): ?>
      <?php $ok = (int) $row['is_correct'] === 1; ?>
      <tr>
        <td><?= nl2br(htmlspecialchars((string) $row['question_text'], ENT_QUOTES, 'UTF-8')) ?></td>
        <td class="<?= $ok ? 'cell-ok' : 'cell-bad' ?>"><?= htmlspecialchars((string) $row['selected_text'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $row['correct_text'], ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <p><a href="<?= htmlspecialchars(url('student/home'), ENT_QUOTES, 'UTF-8') ?>">Back to quizzes</a></p>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
