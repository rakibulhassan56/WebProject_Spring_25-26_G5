<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Quiz Result</h1>

<h3>
<?php echo $quiz['title']; ?>
</h3>

<p>
Score:
<?php echo $attempt['score']; ?>
/
<?php echo $totalMarks; ?>
</p>

<p>
Completed At:
<?php echo $attempt['completed_at']; ?>
</p>

<h2>Answer Review</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>Question</th>
        <th>Your Answer</th>
        <th>Correct Answer</th>
        <th>Marks</th>
        <th>Status</th>
    </tr>

    <?php foreach($resultDetails as $detail): ?>

    <tr>
        <td>
            <?php echo $detail['question_text']; ?>
        </td>

        <td>
            <?php echo $detail['selected_answer']; ?>
        </td>

        <td>
            <?php echo $detail['correct_answer']; ?>
        </td>

        <td>
            <?php echo $detail['marks']; ?>
        </td>

        <td>
            <?php echo $detail['is_correct'] ? 'Correct' : 'Wrong'; ?>
        </td>
    </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="index.php?url=my-results">
    My Results
</a>

<br><br>

<a href="index.php?url=student-quizzes">
    Back to Quiz List
</a>

</body>
</html>
