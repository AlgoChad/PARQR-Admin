<?php
require '../vendor/autoload.php';

session_start();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    try {
        $auth = $firebase->getAuth();
        $auth->sendPasswordResetLink($email);

        // Password reset link has been sent successfully
        echo 'Password reset link has been sent to your email address.';
    } catch (\Kreait\Firebase\Exception\AuthException $e) {
        // Error occurred while sending password reset link
        echo 'An error occurred. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            background-color: #213A5C;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 320px;
        }

        .card h2 {
            font-size: 24px;
            font-weight: bold;
            color: #213A5C;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            color: #213A5C;
            margin-bottom: 5px;
        }

        .form-group input[type="email"] {
            width: 100%;
            height: 40px;
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid lightgray;
        }

        .form-group .btn {
            border: none;
            outline: none;
            height: 40px;
            width: 100%;
            background-color: #213A5C;
            color: white;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .form-group .btn:hover {
            background-color: white;
            border: 1px solid;
            color: #213A5C;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Forgot Password</h2>
        <form method="post" action="forgot_password.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">Enter Your Email:</label>
                <input class="form-control" type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <button type="submit" name="submit" class="btn">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>

