<?php
// Initialize variables to handle success and user existence status
$success = false;
$user_exists = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';

    // Fetch form data
    $username = $_POST['Username'];
    $name = $_POST['Name'];
    $password = $_POST['Password'];
    $mail = $_POST['Mail'];
    $sem_no = $_POST['Sem_no'];
    $course_count = $_POST['Course_count'];
    $CGPA = $_POST['CGPA'];

    // Check if the user already exists
    $sql = "SELECT * FROM `student` WHERE `Username` = '$username'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $num = mysqli_num_rows($result);
        if ($num > 0) { // If a user exists
            $user_exists = true; // Set flag to true
        } else { // If no user exists, insert the new user into the database
            $sql = "INSERT INTO `student` (`Username`, `Name`, `Password`, `Mail`, `Sem_no`, `Course_count`, `CGPA`)
                    VALUES ('$username', '$name', '$password', '$mail', '$sem_no', '$course_count', '$CGPA')";
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
    <title>Student Registration</title>
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

.registration-box {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    color: white;
    max-width: 400px;
}

.registration-box h2 {
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 20px;
}

.alert {
    margin-top: 20px;
}

label {
    display: block;
    text-align: left;
}

input {
    display: block;
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    margin-bottom: 10px;
    background-color: rgba(255, 255, 255, 0.3); /* Transparent background for inputs */
}

input:focus {
    outline: none;
    border: 2px solid #28a745;
    background-color: rgba(255, 255, 255, 0.5); /* Semi-transparent background when focused */
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    width: 100%;
}

button:hover {
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
    <div class="title">Studex Fusion</div>
    <div class="registration-box"> <!-- Main registration box -->
        <h2>Student Registration</h2> <!-- Registration title -->

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

        <form action="student_sign.php" method="post"> <!-- Form for registration -->
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

            <label for="semester">Semester No:</label> <!-- Semester label -->
            <input type="number" id="semester" name="Sem_no" required> <!-- Semester input -->

            <label for="course_count">Number of Courses:</label> <!-- Course Count label -->
            <input type="number" id="course_count" name="Course_count" required> <!-- Course Count input -->

            <label for="cgpa">CGPA:</label> <!-- CGPA label -->
            <input type="number" step="0.01" id="cgpa" name="CGPA" required> <!-- CGPA input -->

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
