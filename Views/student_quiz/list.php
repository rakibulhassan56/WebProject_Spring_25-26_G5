<!DOCTYPE html>
<html>
<head>
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Available Quizzes</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Time</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach($quizzes as $quiz): ?>

    <?php $attempt = $attempts[$quiz['id']] ?? false; ?>

    <tr>

        <td>
            <?php echo $quiz['title']; ?>
        </td>

        <td>
            <?php echo $quiz['description']; ?>
        </td>

        <td>
            <?php echo $quiz['time_limit_minutes']; ?>
            mins
        </td>

        <td>
            <?php echo $attempt && $attempt['completed_at'] ? 'Completed' : 'Available'; ?>
        </td>

        <td>
            <?php if($attempt && $attempt['completed_at']): ?>

                <a href="index.php?url=quiz-result&attempt_id=<?php echo $attempt['id']; ?>">
                    View Result
                </a>

            <?php else: ?>

                <a href="index.php?url=start-quiz&quiz_id=<?php echo $quiz['id']; ?>">
                    Start Quiz
                </a>

            <?php endif; ?>
        </td>

    </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="index.php?url=student-dashboard">
    Dashboard
</a>

</body>
</html>
