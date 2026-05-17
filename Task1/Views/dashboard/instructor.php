<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Instructor Dashboard</h1>

<h3>
Welcome,
<?php echo $_SESSION['name']; ?>
</h3>

<br>

<a href="index.php?url=create-quiz">
    Create Quiz
</a>

<br><br>

<a href="index.php?url=quiz-list">
    My Quizzes
</a>

<br><br>

<a href="index.php?url=instructor-analytics">
    Analytics
</a>

<br><br>

<a href="index.php?url=leaderboard">
    Leaderboard
</a>

<br><br>

<a href="index.php?url=logout">
    Logout
</a>

</body>
</html>
