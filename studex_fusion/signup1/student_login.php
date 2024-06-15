<?php

$login = 0; // Default value for login status
$invalid = 0; // Default value for invalid status

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php'; // Connect to the database

    $username = $_POST['Username'];
    $password = $_POST['Password'];

    // SQL query to check if the username and password exist in the database
    $sql = "SELECT * FROM `student` WHERE `username` = ? AND `password` = ?";
    $stmt = $con->prepare($sql); // Prepare the statement
    $stmt->bind_param("ss", $username, $password); // Bind the parameters
    $stmt->execute(); // Execute the statement
    $result = $stmt->get_result(); // Get the result

    if ($result->num_rows > 0) { // If there's at least one row
        $login = 1; // Set login status to true
        session_start(); // Start session

        $session_lifetime = 60 * 60 * 24 * 7; // 7 days
        session_set_cookie_params($session_lifetime); // Set session cookie lifetime
        session_regenerate_id(true); // Regenerate the session ID for security

        $_SESSION['username'] = $username; // Store the username in session
        header('Location: student_home.php'); // Redirect to the home page
    } else {
        $invalid = 1; // Set invalid status to true if credentials are incorrect
    }

    $stmt->close(); // Close the statement
    $con->close(); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Your CSS styles for the login page */
        body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: rgba(0, 76, 76);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    position: relative;
}

.title {
    font-size: 3em;
    font-weight: bold;
    margin-bottom: 20px;
    color: #fff;
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
}

.login-box {
    max-width: 400px;
    padding: 40px;
    background-color: rgba(0, 128, 128, 0.7);
    border-radius: 20px;
    text-align: center;
    color: white;
}

.login-box h2 {
    font-size: 2em;
    margin-bottom: 20px;
}

.image-container {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 200px;
    height: auto;
    border-radius: 10px;
    overflow: hidden;
}

.image-container img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.form-group {
    margin-bottom: 15px;
}

input {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
}

input:focus {
    outline: none;
    border: 2px solid #28a745;
}

button {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s;
    width: 100%;
}

button:hover {
    background-color: #45a049;
}

.sign-up {
    font-size: 1.1em;
    color: #fff;
    text-decoration: none;
    transition: color 0.3s;
}

.sign-up:hover {
    text-decoration: underline;
}


    </style>
</head>
<body>

<div class="title">Studex Fusion</div> <!-- Positioning the title -->

<div class="login-box"> <!-- The shaded login box -->
    <h2>Student Login</h2> <!-- Title above the login form -->

    <?php
    // Display success message if login is successful
    if ($login) {
        echo '<div class="alert alert-success" role="alert">
                <strong>Success!</strong> You are successfully logged in.
              </div>';
    }

    // Display error message if login is invalid
    if ($invalid) {
        echo '<div class="alert alert-danger" role="alert" style="background-color: #ff6666; padding: 10px; border-radius: 5px; text-align: center;">
                <strong>Error!</strong> Invalid Credentials.
              </div>';
    }
    ?>
    <div class="image-container">
        <img src="logo.jpg" alt="Image">
    </div>
    <!-- Login form with consistent input field widths -->
    <form action="student_login.php" method="post"> 
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="Username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="Password" required>
        </div>

        <button type="submit">Log In</button>

        <!-- Sign-up link if the user doesn't have an account -->
        <a href="signup.php" class="sign-up">Don't have an account? Sign up!</a>
    </form>
</div>

</body>
</html>
