<!DOCTYPE html>
<html>
<head>
    <title>My Results</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>My Results</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Quiz</th>
        <th>Score</th>
        <th>Total Marks</th>
        <th>Completed At</th>
        <th>Action</th>
    </tr>

    <?php foreach($results as $result): ?>

    <tr>
        <td>
            <?php echo $result['title']; ?>
        </td>

        <td>
            <?php echo $result['score']; ?>
        </td>

        <td>
            <?php echo $result['total_marks']; ?>
        </td>

        <td>
            <?php echo $result['completed_at']; ?>
        </td>

        <td>
            <a href="index.php?url=quiz-result&attempt_id=<?php echo $result['id']; ?>">
                View Result
            </a>
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
