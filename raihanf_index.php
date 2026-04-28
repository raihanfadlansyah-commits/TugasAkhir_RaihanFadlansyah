<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Perpustakaan</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            background: #2c3e50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #34495e;
        }

        .pesan {
            margin-top: 10px;
            color: red;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Login Perpustakaan</h2>

    <form method="POST" action="proses_login.php" autocomplete="off">
        <input type="text" name="username" placeholder="Username / ID" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <?php
    if (isset($_SESSION['pesan'])) {
        echo "<p class='pesan'>" . htmlspecialchars($_SESSION['pesan']) . "</p>";
        unset($_SESSION['pesan']);
    }
    ?>
</div>

</body>
</html>