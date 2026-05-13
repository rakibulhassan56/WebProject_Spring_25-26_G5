<?php
/** @var array $quizzes */
/** @var int $selected_id */
/** @var array|null $selected_quiz */
/** @var array $attempts */
/** @var array|null $summary */
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Instructor analytics</h1>
  <form method="get" action="<?= htmlspecialchars(url('instructor/analytics'), ENT_QUOTES, 'UTF-8') ?>" class="inline">
    <label>Quiz
      <select name="quiz_id" onchange="this.form.submit()">
        <option value="0">— Select —</option>
        <?php foreach ($quizzes as $q): ?>
          <option value="<?= (int) $q['id'] ?>" <?= $selected_id === (int) $q['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars((string) $q['title'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
  </form>

  <?php if ($selected_quiz): ?>
    <h2><?= htmlspecialchars((string) $selected_quiz['title'], ENT_QUOTES, 'UTF-8') ?></h2>
    <?php $totalMarks = (int) $selected_quiz['total_marks']; ?>
    <table class="data">
      <thead>
        <tr><th>Student</th><th>Score</th><th>Duration</th><th>Pass/Fail</th></tr>
      </thead>
      <tbody>
      <?php foreach ($attempts as $a): ?>
        <?php
        $score = (int) $a['score'];
        $thr = $totalMarks > 0 ? (int) floor(0.6 * $totalMarks) : 0;
        $pass = $totalMarks > 0 && $score >= $thr;
        $start = new DateTimeImmutable((string) $a['started_at']);
        $end = new DateTimeImmutable((string) $a['completed_at']);
        $dur = $start->diff($end)->format('%H:%I:%S');
        ?>
        <tr>
          <td><?= htmlspecialchars((string) $a['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= $score ?> / <?= $totalMarks ?></td>
          <td><?= htmlspecialchars($dur, ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge <?= $pass ? 'badge-ok' : 'badge-bad' ?>"><?= $pass ? 'Pass' : 'Fail' ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <?php if ($summary && $summary['count'] > 0): ?>
      <table class="data summary">
        <tbody>
          <tr><th>Class average</th><td><?= htmlspecialchars((string) $summary['average'], ENT_QUOTES, 'UTF-8') ?></td></tr>
          <tr><th>Highest score</th><td><?= (int) $summary['highest'] ?></td></tr>
          <tr><th>Lowest score</th><td><?= (int) $summary['lowest'] ?></td></tr>
          <tr><th>Pass rate</th><td><?= htmlspecialchars((string) $summary['pass_rate'], ENT_QUOTES, 'UTF-8') ?>%</td></tr>
        </tbody>
      </table>
    <?php elseif ($selected_quiz): ?>
      <p>No completed attempts yet.</p>
    <?php endif; ?>
  <?php elseif ($selected_id > 0): ?>
    <p class="error">Quiz not found.</p>
  <?php endif; ?>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
