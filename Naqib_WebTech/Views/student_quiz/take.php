<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>
<?php echo $quiz['title']; ?>
</h1>

<h3>
Time Left:
<span id="timer"></span>
</h3>

<form method="POST"
      id="quiz-form"
      data-time-limit="<?php echo (int)$quiz['time_limit_minutes']; ?>"
      action="index.php?url=submit-quiz&attempt_id=<?php echo $attempt['id']; ?>">

    <?php foreach($questions as $question): ?>

        <div>

            <h3>
                <?php echo $question['question_text']; ?>
            </h3>

            <p>
                Marks:
                <?php echo $question['marks']; ?>
            </p>

            <?php foreach($optionsByQuestion[$question['id']] as $option): ?>

                <label>
                    <input type="radio"
                           name="answers[<?php echo $question['id']; ?>]"
                           value="<?php echo $option['id']; ?>">

                    <?php echo $option['option_text']; ?>
                </label>

                <br>

            <?php endforeach; ?>

        </div>

        <hr>

    <?php endforeach; ?>

    <button type="submit">
        Submit Quiz
    </button>

</form>

<script src="assets/js/quiz-timer.js"></script>

</body>
</html>
