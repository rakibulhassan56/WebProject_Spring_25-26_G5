<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .auth-box {
            width: min(90%, 420px);
            padding: 30px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #dfe3e8;
            border-radius: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        button {
            cursor: pointer;
        }

        label {
            display: inline-block;
            margin: 0 8px;
        }
    </style>
</head>
<body>

<div class="auth-box">

<h1>Register</h1>

<form method="POST">

    <input type="text"
           name="name"
           placeholder="Enter Name">
    <br><br>

    <input type="email"
           name="email"
           placeholder="Enter Email">
    <br><br>

    <input type="password"
           name="password"
           placeholder="Enter Password">
    <br><br>

    <label>
        <input type="radio"
               name="role"
               value="student">
        Student
    </label>

    <label>
        <input type="radio"
               name="role"
               value="instructor">
        Instructor
    </label>

    <br><br>

    <button type="submit">
        Register
    </button>

</form>

<br>

<a href="index.php?url=login">
    Back to Login
</a>

</div>

</body>
</html>
