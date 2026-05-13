<?php
/** @var array $users */
$extraHead = '<script src="/assets/js/admin_users.js" defer></script>';
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>User management</h1>
  <table class="data" id="users-table">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr data-user-id="<?= (int) $u['id'] ?>">
        <td><?= htmlspecialchars((string) $u['name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $u['email'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $u['role'], ENT_QUOTES, 'UTF-8') ?></td>
        <td class="status-cell"><?= (int) $u['is_active'] === 1 ? 'Active' : 'Suspended' ?></td>
        <td>
          <?php if ((int) $u['id'] === (int) ($_SESSION['user_id'] ?? 0)): ?>
            <em>you</em>
          <?php else: ?>
            <button type="button" class="js-toggle-user" data-active="<?= (int) $u['is_active'] ?>">
              <?= (int) $u['is_active'] === 1 ? 'Suspend' : 'Activate' ?>
            </button>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <script>window.__USER_TOGGLE_URL__ = <?= json_encode(url('api/users/toggle'), JSON_THROW_ON_ERROR) ?>;</script>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
