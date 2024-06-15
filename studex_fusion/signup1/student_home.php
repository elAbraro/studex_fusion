<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Information</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('login.jpg'); /* Background image */
            background-size: cover;
            color: #000;
            position: relative; /* Required for absolute positioning */
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height of the viewport */
            flex-direction: column; /* Stack items vertically */
        }
        .welcome-message {
            font-size: 36px; /* Larger font size */
            text-align: center; /* Center-align text */
            margin-bottom: 20px; /* Add some spacing */
            font-weight: bold;
        }
        .menu-container {
            width: 70%;
            padding: 20px;
            background-color: #f0f0f0; /* Light gray background color */
            border-radius: 10px;
            margin-top: 20px; /* Add some spacing */
        }
        .menu {
            list-style-type: none;
            padding: 0;
            font-size: 20px; /* Larger font size for menu items */
        }
        .menu li {
            margin-bottom: 10px;
        }
        .menu li a {
            color: #333; /* Dark text color */
            text-decoration: none;
            font-weight: bold;
            display: block; /* Make menu items block-level */
            padding: 10px; /* Add padding for spacing */
            border-radius: 5px; /* Add rounded corners */
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        .menu li a:hover {
            background-color: #ddd; /* Light gray background color on hover */
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
        .title {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="title">Studex Fusion</div>
    <div class="container">
        <?php
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: student_login.php"); // Redirect to login page if not logged in
            exit();
        }

        // Display welcome message with username
        echo "<div class='welcome-message'>Welcome, " . $_SESSION["username"] .'!!!'. "</div>";
        echo 'Student Dashboard';
        // Include the database connection file
        include 'connect.php';
        ?>
        <div class="image-container">
            <img src="logo.jpg" alt="Image">
        </div>
        <div class="menu-container">
            <ul class="menu">
                <li><a href="stu_info.php">Info</a></li>
                <li><a href="student_course.php">Course</a></li>
                <li><a href="student_contact.php">Contact</a></li>
                <li><a href="student_deadline.php">Deadline</a></li>
                <li><a href="marks.php">Marks</a></li> <!-- Marks section -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <div class="footer">Made with VSCode</div>
</body>
</html>
