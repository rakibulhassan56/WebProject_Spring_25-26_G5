<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Leaderboard</h1>

<label>
    Quiz:
    <select id="quiz-filter" onchange="loadLeaderboard()">
        <option value="">
            All Quizzes
        </option>

        <?php foreach($quizzes as $quiz): ?>

            <option value="<?php echo $quiz['id']; ?>"
                <?php echo $quiz_id == $quiz['id'] ? 'selected' : ''; ?>>
                <?php echo $quiz['title']; ?>
            </option>

        <?php endforeach; ?>
    </select>
</label>

<br><br>

<table border="1" cellpadding="10">

    <thead>
        <tr>
            <th>Rank</th>
            <th>Student</th>
            <th>Quiz</th>
            <th>Score</th>
            <th>Total Marks</th>
            <th>Completed At</th>
        </tr>
    </thead>

    <tbody id="leaderboard-body">
        <?php $rank = 1; ?>

        <?php foreach($leaderboard as $row): ?>

        <tr>
            <td>
                <?php echo $rank; ?>
            </td>

            <td>
                <?php echo $row['name']; ?>
            </td>

            <td>
                <?php echo $row['title']; ?>
            </td>

            <td>
                <?php echo $row['score']; ?>
            </td>

            <td>
                <?php echo $row['total_marks']; ?>
            </td>

            <td>
                <?php echo $row['completed_at']; ?>
            </td>
        </tr>

        <?php $rank++; ?>

        <?php endforeach; ?>
    </tbody>

</table>

<br>

<a href="index.php?url=<?php echo $_SESSION['role']; ?>-dashboard">
    Dashboard
</a>

<script src="assets/js/leaderboard.js"></script>

</body>
</html>
