<?php
// Start the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the index.php page after logout
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        /* Global styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa; /* Background color */
            color: #333; /* Text color */
        }

        /* Logo container */
        .logo-container {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
        }

        /* Logo styling */
        .logo {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }

        /* Logout message container */
        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Logout message styling */
        .logout-message {
            text-align: center;
            padding: 20px;
            background-color: #ffffff; /* White background */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Box shadow */
        }
    </style>
</head>
<body>
    <!-- Logo container at the top right corner -->
    <div class="logo-container">
        <img class="logo" src="logo.jpg" alt="Logo">
    </div>

    <!-- Logout message container -->
    <div class="logout-container">
        <div class="logout-message">
            <h1>Logged Out Successfully</h1>
            <p>You have been successfully logged out. Redirecting you to the homepage...</p>
        </div>
    </div>
</body>
</html>
