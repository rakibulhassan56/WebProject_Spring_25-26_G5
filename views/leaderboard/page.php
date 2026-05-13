<?php
$extraHead = '<script src="/assets/js/leaderboard.js" defer></script>';
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Leaderboard <span id="lb-countdown" class="muted small"></span></h1>
  <p class="muted">Top 10 students by cumulative score (completed attempts only). Auto-refreshes every 30 seconds.</p>
  <table class="data" id="leaderboard-table">
    <thead><tr><th>#</th><th>Student</th><th>Cumulative score</th></tr></thead>
    <tbody></tbody>
  </table>
  <script>window.__LEADERBOARD_URL__ = <?= json_encode(url('api/leaderboard'), JSON_THROW_ON_ERROR) ?>;</script>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
