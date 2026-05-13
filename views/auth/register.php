<?php
/** @var array $errors */
/** @var array $old */
require base_path('views/layouts/header.php');
?>
<main class="container narrow">
  <h1>Create account</h1>
  <?php if (!empty($errors['form'])): ?>
    <p class="error"><?= htmlspecialchars($errors['form'], ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>
  <form method="post" action="<?= htmlspecialchars(url('auth/register'), ENT_QUOTES, 'UTF-8') ?>" class="stack">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <label>Name
      <input name="name" value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      <?php if (!empty($errors['name'])): ?><span class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Email
      <input type="email" name="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      <?php if (!empty($errors['email'])): ?><span class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <label>Password (min 8 characters)
      <input type="password" name="password" required minlength="8">
      <?php if (!empty($errors['password'])): ?><span class="error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
    </label>
    <fieldset>
      <legend>Role</legend>
      <?php if (!empty($errors['role'])): ?><p class="error"><?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
      <label><input type="radio" name="role" value="student" <?= (($old['role'] ?? '') === 'instructor') ? '' : 'checked' ?>> Student</label>
      <label><input type="radio" name="role" value="instructor" <?= (($old['role'] ?? '') === 'instructor') ? 'checked' : '' ?>> Instructor</label>
    </fieldset>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="<?= htmlspecialchars(url('auth/login'), ENT_QUOTES, 'UTF-8') ?>">Login</a></p>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
