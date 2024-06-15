<?php
$login = 0;
$invalid = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';  

    $username = $_POST['Username'];
    $password = $_POST['Password'];

    
    $sql = "SELECT * FROM `teacher` WHERE `username` = '$username' AND `password` = '$password'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $login = 1;
            session_start();
            // Fetch the unique_int and store it in the session
            $row = mysqli_fetch_assoc($result);
            $_SESSION['unique_int'] = $row['unique_int'];
            $_SESSION['username'] = $username;
            header('location:teacher_home.php');
        } else {
            $invalid = 1;
        }
    } 
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
        body {
    background-color: rgba(0, 76, 76);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 40px; /* Increased padding for better spacing */
    border-radius: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    text-align: center;
    color: white;
    font-size: 1.2em;
    max-width: 400px; /* Adjusted maximum width */
    margin: auto; /* Center the login box horizontally */
}

.title {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 1.5em;
    font-weight: bold;
    color: white; /* Changed title color to white */
}

.form-group {
    margin-bottom: 20px; /* Increased margin between form groups */
}

input {
    width: calc(100% - 20px); /* Adjusted input width to accommodate padding */
    padding: 10px;
    border: none;
    border-radius: 5px;
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
    width: calc(100% - 20px); /* Adjusted button width to accommodate padding */
}

button:hover {
    background-color: #218838;
}

.sign-up {
    font-size: 0.8em;
    color: white;
    text-decoration: none;
    display: block;
    margin-top: 15px;
}

.sign-up:hover {
    text-decoration: underline;
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

<div class="title">Studex Fusion</div> <!-- Positioning the title at the top left -->

<div class="login-box"> <!-- The shaded login box -->
    <h2>Teacher Login</h2> <!-- Title above the login form -->

    <?php
    // Display success message if login is successful
    if ($login) {
        echo '<div class="alert alert-success" role="alert">
                <strong>Success!</strong> You are successfully logged in.
              </div>';
    }

    // Display error message if login is invalid without the cross button
    if ($invalid) {
        echo '<div class="alert alert-danger" role="alert" style="background-color: #ff6666; padding: 10px; border-radius: 5px; text-align: center;">
                <strong>Error!</strong> Invalid Credentials.
              </div>';
    }
    ?>

    <!-- Login form with consistent input field widths -->
    <form action="teacher_login.php" method="post"> 
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="Username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="Password" required>
        </div>

        <button type="submit">Log In</button>
        <a href="signup.php" class="sign-up">Don't have an account? Sign up!</a>
    </form>
</div>
<div class="image-container">
    <img src="logo.jpg" alt="Image">
</div>

</body>
</html>
