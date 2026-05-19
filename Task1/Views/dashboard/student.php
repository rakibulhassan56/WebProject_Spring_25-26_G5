<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Student Dashboard</h1>

<h3>
Welcome,
<?php echo $_SESSION['name']; ?>
</h3>

<a href="index.php?url=student-quizzes">
    Available Quizzes
</a>

<br><br>

<a href="index.php?url=my-results">
    My Results
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
