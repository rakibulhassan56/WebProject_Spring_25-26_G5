<!DOCTYPE html>
<html>
<head>
    <title>Instructor Analytics</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Instructor Analytics</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Quiz</th>
        <th>Attempts</th>
        <th>Average Score</th>
        <th>Highest Score</th>
        <th>Lowest Score</th>
        <th>Total Marks</th>
    </tr>

    <?php foreach($analytics as $row): ?>

    <tr>
        <td>
            <?php echo $row['title']; ?>
        </td>

        <td>
            <?php echo $row['attempt_count']; ?>
        </td>

        <td>
            <?php echo $row['average_score'] ? round($row['average_score'], 2) : 0; ?>
        </td>

        <td>
            <?php echo $row['highest_score'] ?? 0; ?>
        </td>

        <td>
            <?php echo $row['lowest_score'] ?? 0; ?>
        </td>

        <td>
            <?php echo $row['total_marks'] ?? 0; ?>
        </td>
    </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="index.php?url=instructor-dashboard">
    Dashboard
</a>

</body>
</html>
