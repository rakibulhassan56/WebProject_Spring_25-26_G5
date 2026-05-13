<?php
/** @var string $title */
$role = $_SESSION['role'] ?? null;
$uid = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Assessment', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <?php if (!empty($extraHead)) {
      echo $extraHead;
  } ?>
</head>
<body>
<header class="site-header">
  <div class="brand"><a href="<?= htmlspecialchars(url('home'), ENT_QUOTES, 'UTF-8') ?>">Assessment</a></div>
  <nav>
    <?php if ($uid): ?>
      <span class="who"><?= htmlspecialchars((string) ($_SESSION['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
      <?php if ($role === 'student'): ?>
        <a href="<?= htmlspecialchars(url('student/home'), ENT_QUOTES, 'UTF-8') ?>">Home</a>
        <a href="<?= htmlspecialchars(url('student/results'), ENT_QUOTES, 'UTF-8') ?>">My results</a>
      <?php elseif ($role === 'instructor'): ?>
        <a href="<?= htmlspecialchars(url('instructor/home'), ENT_QUOTES, 'UTF-8') ?>">Home</a>
        <a href="<?= htmlspecialchars(url('instructor/quizzes'), ENT_QUOTES, 'UTF-8') ?>">Quizzes</a>
        <a href="<?= htmlspecialchars(url('instructor/analytics'), ENT_QUOTES, 'UTF-8') ?>">Analytics</a>
      <?php elseif ($role === 'admin'): ?>
        <a href="<?= htmlspecialchars(url('admin/users'), ENT_QUOTES, 'UTF-8') ?>">Users</a>
      <?php endif; ?>
      <a href="<?= htmlspecialchars(url('leaderboard'), ENT_QUOTES, 'UTF-8') ?>">Leaderboard</a>
      <a href="<?= htmlspecialchars(url('auth/logout'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
    <?php else: ?>
      <a href="<?= htmlspecialchars(url('auth/login'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
      <a href="<?= htmlspecialchars(url('auth/register'), ENT_QUOTES, 'UTF-8') ?>">Register</a>
      <a href="<?= htmlspecialchars(url('leaderboard'), ENT_QUOTES, 'UTF-8') ?>">Leaderboard</a>
    <?php endif; ?>
  </nav>
</header>
