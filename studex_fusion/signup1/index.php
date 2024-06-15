<?php
session_start();
require_once 'connect.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'user_type' key exists in the $_POST array
    if (isset($_POST['user_type'])) {
        $userType = $_POST['user_type'];

        switch ($userType) {
            case 'student':
                header('Location: student_login.php');
                exit;
            case 'teacher':
                header('Location: teacher_login.php');
                exit;
            default:
                $error_message = "Please select an option to login.";
        }
    } else {
        $error_message = "Please select an option to login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('WWW.jpg');
            background-size: cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            position: relative; /* Added for positioning */
        }

        .title {
            font-size: 3em;
            font-weight: bold;
            margin-bottom: 20px;
            color: #fff;
            position: absolute; /* Positioned at the top center */
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .container {
            max-width: 400px;
            padding: 40px;
            background-color:rgb(0, 33, 94); /* Adjust transparency here */
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0;
            cursor: pointer;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + label {
            position: relative;
            padding-left: 30px;
            font-size: 1.2em;
            color: #ddd;
            transition: color 0.3s;
        }

        input[type="radio"] + label:before {
            content: "";
            position: absolute;
            left: 0;
            top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #fff;
            transition: background-color 0.3s;
        }

        input[type="radio"]:checked + label {
            color: #fff;
        }

        input[type="radio"]:checked + label:before {
            background-color: #4CAF50;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 15px 40px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            margin-top: 20px;
            font-size: 1.1em;
            color: #ddd;
        }

        a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #45a049;
        }

        .image-container {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 200px; /* Adjust image width */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px;
            overflow: hidden; /* Hide overflowing content */
        }
        .image-container img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .bottom-right {
            font-size: 0.8em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="title">Studex Fusion</div> <!-- Title placed at the top center of the page -->
    <div class="container">
        <h1>Login</h1>
        <form action="index.php" method="post">
            <input type="radio" id="student" name="user_type" value="student">
            <label for="student">I'm a student</label>
            <br>
            <input type="radio" id="teacher" name="user_type" value="teacher">
            <label for="teacher">I'm a teacher</label>
            <br>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>

    <div class="image-container">
        <img src="logo.jpg" alt="Image">
    </div>
</body>
</html>
