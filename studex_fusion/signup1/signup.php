<?php
// PHP block for handling POST requests and redirects based on user type
session_start(); // Start the session
require_once 'connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the request method is POST
    $userType = $_POST['user_type']; // Get the selected user type

    // Redirect to the appropriate page based on user type
    switch ($userType) {
        case 'student':
            header('Location: student_sign.php'); // Redirect to student sign-up
            exit; // Ensure the script stops after the redirect
        case 'teacher':
            header('Location: teacher_sign.php'); // Redirect to teacher sign-up
            exit; // Ensure the script stops after the redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensure responsive design -->
    <title>User Type Selection</title> <!-- Page title -->
    <style>
        body {
    background-color: rgba(0,76,76);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    color: white;
}

.title {
    font-size: 3em;
    font-weight: bold;
    color: white;
    position: absolute;
    top: 10px;
    left: 10px;
}

.signup-container {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 40px; /* Increase padding for better spacing */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 300px; /* Limit maximum width */
    text-align: center;
    color: white;
    margin: auto; /* Center the container horizontally */
}

.signup-container h1 {
    font-size: 1.5em;
    text-align: center;
    margin-bottom: 20px;
    color: white;
    font-weight: bold;
}

form {
    text-align: center; /* Center the form elements */
}

select {
    width: 100%; /* Make select box full width */
    padding: 8px; /* Padding for the select box */
    border-radius: 5px; /* Rounded corners for the select box */
    margin-bottom: 20px; /* Increase margin for better spacing */
}

select {
    padding: 8px;
    border-radius: 5px;
}

input[type="submit"] {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
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

    </style>
</head>
<body>
    <!-- Centered and shaded login box -->
    <div class="signup-container"> 
        <!-- Sign Up title at the top center of the container -->
        <h1>Sign Up</h1>

        <form action="signup.php" method="POST"> <!-- POST method for form -->
            <label for="user_type">User Type:</label> <!-- Label for the select box -->
            <select name="user_type" id="user_type"> <!-- Dropdown for user type -->
                <option value="student">Student</option> <!-- Student option -->
                <option value="teacher">Teacher</option> <!-- Teacher option -->
            </select> <!-- End of the select box -->
            <input type="submit" value="Sign Up"> <!-- Submit button -->
        </form>
    </div><!-- End of the signup-container -->
    <div class="image-container">
        <img src="logo.jpg" alt="Image">
    </div> 
</body>
</html>
