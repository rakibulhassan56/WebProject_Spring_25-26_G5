<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1>Admin Dashboard</h1>

<h3>
Welcome,
<?php echo $_SESSION['name']; ?>
</h3>

<a href="index.php?url=logout">
    Logout
</a>

</body>
</html>
