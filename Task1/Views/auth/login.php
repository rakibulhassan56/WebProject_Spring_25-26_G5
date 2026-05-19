<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            width: min(90%, 380px);
            padding: 30px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #dfe3e8;
            border-radius: 8px;
        }

        input,
        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="auth-box">

<h1>Login</h1>

<form method="POST">

    <input type="email"
           name="email"
           placeholder="Enter Email"
           required>

    <br><br>

    <input type="password"
           name="password"
           placeholder="Enter Password"
           required>

    <br><br>

    <button type="submit">
        Login
    </button>

</form>

<br>

<a href="index.php?url=register">
    Register Here
</a>

</div>

</body>
</html>
