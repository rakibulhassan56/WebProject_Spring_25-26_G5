<?php
/** @var array $rows */
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>My results</h1>
  <table class="data">
    <thead>
      <tr><th>Quiz</th><th>Score</th><th>Date</th><th>Duration</th><th>Result</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <?php
      $total = (int) $r['total_marks'];
      $score = (int) $r['score'];
      $thr = $total > 0 ? (int) floor(0.6 * $total) : 0;
      $pass = $total > 0 && $score >= $thr;
      $start = new DateTimeImmutable((string) $r['started_at']);
      $end = new DateTimeImmutable((string) $r['completed_at']);
      $dur = $start->diff($end);
      $durStr = $dur->format('%H:%I:%S');
      ?>
      <tr>
        <td><?= htmlspecialchars((string) $r['quiz_title'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= $score ?> / <?= $total ?></td>
        <td><?= htmlspecialchars($end->format('Y-m-d H:i'), ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($durStr, ENT_QUOTES, 'UTF-8') ?></td>
        <td><span class="badge <?= $pass ? 'badge-ok' : 'badge-bad' ?>"><?= $pass ? 'Pass' : 'Fail' ?></span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
