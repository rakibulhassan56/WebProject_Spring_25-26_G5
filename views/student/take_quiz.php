<?php
/** @var array $attempt */
/** @var array $questions */
$minutes = (int) ($attempt['time_limit_minutes'] ?? 0);
$extraHead = '<script src="/assets/js/student_quiz.js" defer></script>';
require base_path('views/layouts/header.php');
?>
<main class="container" id="quiz-root" data-minutes="<?= $minutes ?>">
  <div class="quiz-head">
    <h1><?= htmlspecialchars((string) $attempt['quiz_title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="timer" id="timer-display" aria-live="polite">--:--</div>
  </div>
  <div id="timeup-banner" class="banner warn hidden">⏰ Time's up!</div>

  <form id="quiz-form" class="stack" data-submit-url="<?= htmlspecialchars(url('api/quiz/submit'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="attempt_id" value="<?= (int) $attempt['id'] ?>">
    <?php foreach ($questions as $qi => $qu): ?>
      <fieldset class="question-card">
        <legend>Question <?= $qi + 1 ?> (<?= (int) $qu['marks'] ?> marks)</legend>
        <p><?= nl2br(htmlspecialchars((string) $qu['question_text'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php foreach ($qu['options'] as $opt): ?>
          <label class="option">
            <input type="radio" name="q_<?= (int) $qu['id'] ?>" value="<?= (int) $opt['id'] ?>" required>
            <?= htmlspecialchars((string) $opt['option_text'], ENT_QUOTES, 'UTF-8') ?>
          </label>
        <?php endforeach; ?>
      </fieldset>
    <?php endforeach; ?>
    <button type="submit" id="submit-quiz">Submit Quiz</button>
    <p id="quiz-msg" class="error hidden"></p>
  </form>
</main>
<?php require base_path('views/layouts/footer.php'); ?>
