<?php
/** @var string $title */
$title = $title ?? 'Not found';
http_response_code(404);
require base_path('views/layouts/header.php');
?>
<main class="container">
  <h1>Page not found</h1>
  <p>The route you requested does not exist.</p>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
