<?php
// Initialize flags for success and user existence
$success = false;
$user_exists = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';

    // Fetch form data
    $username = $_POST['Username'];
    $name = $_POST['Name'];
    $password = $_POST['Password'];
    $mail = $_POST['Mail'];
    $unique_int = $_POST['unique_int'];

    // Check if the user already exists
    $sql = "SELECT * from `teacher` WHERE `Username` = '$username'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $num = mysqli_num_rows($result);
        if ($num > 0) { // If a user exists
            $user_exists = true; // Set flag to true
        } else { // If no user exists, insert the new user into the database
            $sql = "INSERT INTO `teacher` (`Username`, `unique_int`, `Password`, `Name`, `Mail`)
                    VALUES ('$username', '$unique_int', '$password', '$name',  '$mail')";
            $result = mysqli_query($con, $sql);

            if ($result) {
                $success = true; // Set success flag to true
            } else {
                die(mysqli_error($con)); // If there's an error during insertion
            }
        }
    } else {
        die(mysqli_error($con)); // If there's an error during the query
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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
}

.registration-box {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    color: white;
    max-width: 400px;
}

.title {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 1.5em;
    font-weight: bold;
    color: white;
}

.alert {
    margin-top: 20px;
}

label {
    display: block;
    text-align: left;
    color: white; /* Changed label color to white */
}

input {
    display: block;
    width: calc(100% - 20px);
    padding: 10px;
    border-radius: 5px;
    border: none;
    margin-bottom: 15px; /* Increased margin between inputs */
    background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
    color: white; /* Changed input text color to white */
}

input:focus {
    outline: none;
    border: 2px solid #28a745;
}

button {
    background-color: #28a745;
    border: none;
    color: white;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

button:hover {
    background-color: #218838;
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
    <!-- Title "Studex Fusion" above the registration box -->
    <div class="title">Studex Fusion</div>

    <div class="registration-box"> <!-- Main registration box -->
        <h2>Teacher Registration</h2> <!-- Registration title -->

        <?php
        if ($user_exists) { // Display alert if user already exists
            echo '<div class="alert alert-danger" role="alert">
                    <strong>Oops!</strong> User already exists!
                </div>';
        }

        if ($success) { // If registration is successful, show alert and then redirect
            echo '<div class="alert alert-success" role="alert">
                    <strong>Success!</strong> You have registered successfully. Redirecting...
                </div>';
            // Redirect to index.php after a short delay
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "index.php";
                    }, 3000); // Redirect after 3 seconds
                </script>';
        }
        ?>

        <form action="teacher_sign.php" method="post"> <!-- Form for registration -->
            <label for="name">Name:</label> <!-- Name label -->
            <input type="text" id="name" name="Name" required> <!-- Name input -->

            <label for="username">Username:</label> <!-- Username label -->
            <input type="text" id="username" name="Username" required> <!-- Username input -->

            <label for="password">Password:</label> <!-- Password label -->
            <input type="password" id="password" name="Password" required> <!-- Password input -->

            <label for="retype_password">Retype Password:</label> <!-- Retype Password label -->
            <input type="password" id="retype_password" name="retype_password" required> <!-- Retype Password input -->

            <label for="email">Email Address:</label> <!-- Email label -->
            <input type="email" id="email" name="Mail" required> <!-- Email input -->

            <label for="unique_int">Unique Initial:</label> <!-- Unique Initial label -->
            <input type="text" id="unique_int" name="unique_int" required> <!-- Unique Initial input -->

            <button type="submit">Register</button> <!-- Register button -->
        </form>

        <!-- Already have an account? Link to login -->
        <p style="margin-top: 20px;">
            Already have an account? <a href="index.php" style="color: #f0f0f0;">Log in</a>
        </p>
    </div> <!-- End of registration box -->
    <div class="image-container">
        <img src="logo.jpg" alt="Image">
    </div>
</body>
</html>
