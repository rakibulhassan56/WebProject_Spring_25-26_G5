<?php
/** @var array $stats */
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Instructor dashboard</h1>
  <section class="widgets">
    <div class="widget"><h3>Quizzes created</h3><p class="big"><?= (int) $stats['quizzes_created'] ?></p></div>
    <div class="widget"><h3>Total attempts (your quizzes)</h3><p class="big"><?= (int) $stats['total_attempts'] ?></p></div>
  </section>
  <p><a class="button" href="<?= htmlspecialchars(url('instructor/quizzes'), ENT_QUOTES, 'UTF-8') ?>">Manage quizzes</a></p>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
