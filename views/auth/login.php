<?php
/** @var array $errors */
/** @var bool $suspended */
require base_path('views/layouts/header.php');
?>
<main class="container narrow">
  <h1>Login</h1>
  <?php if (!empty($suspended)): ?>
    <p class="banner warn">Your account has been suspended.</p>
  <?php endif; ?>
  <?php if (!empty($errors['form'])): ?>
    <p class="error"><?= htmlspecialchars($errors['form'], ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>
  <form method="post" action="<?= htmlspecialchars(url('auth/login'), ENT_QUOTES, 'UTF-8') ?>" class="stack">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <label>Email
      <input type="email" name="email" required>
      <?php if (!empty($errors['email'])): ?><span class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Password
      <input type="password" name="password" required>
      <?php if (!empty($errors['password'])): ?><span class="error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <button type="submit">Login</button>
  </form>
  <p>No account? <a href="<?= htmlspecialchars(url('auth/register'), ENT_QUOTES, 'UTF-8') ?>">Register</a></p>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
