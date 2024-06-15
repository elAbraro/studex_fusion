<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Marks</title>
    <style>
        /* Global styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #334257; /* Ambient background color */
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Add transparency to the container */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); /* Add shadow effect */
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #fff; /* Add border */
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        td {
            color: #fff;
        }

        /* Keyframe animation for table row */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        tr {
            animation: fadeIn 1s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    session_start();

    // Check if the student is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php"); // Redirect to login page if not logged in
        exit();
    }

    // Include database connection file
    include_once "connect.php";

    // Fetch student ID from session
    $username = $_SESSION['username'];

    // Query to fetch st_id from the student table using the username
    $st_id_query = "SELECT st_id FROM student WHERE username = '$username'";
    $st_id_result = mysqli_query($con, $st_id_query);
    $st_id_row = mysqli_fetch_assoc($st_id_result);
    $st_id = $st_id_row['st_id'];

    // Query to fetch all distinct course codes for the student's st_id
    $course_code_query = "SELECT DISTINCT course_code FROM mark WHERE st_id = '$st_id'";
    $course_code_result = mysqli_query($con, $course_code_query);

    // Display marks for each course code
    while ($course_code_row = mysqli_fetch_assoc($course_code_result)) {
        $course_code = $course_code_row['course_code'];

        // Query to fetch mark for the student and course_code
        $marks_query = "SELECT * FROM mark WHERE st_id = '$st_id' AND course_code = '$course_code'";
        $marks_result = mysqli_query($con, $marks_query);

        // Display mark for this course code
        echo "<h1>Student Marks for Course Code: $course_code</h1>";
        echo "<table border='1'>
            <tr>
                <th>Quiz</th>
                <th>Mid</th>
                <th>Assignment</th>
                <th>Attendance</th>
                <th>Final</th>
                <th>Total</th>
            </tr>";

        while ($row = mysqli_fetch_assoc($marks_result)) {
            echo "<tr>";
            echo "<td>" . $row['quiz'] . "</td>";
            echo "<td>" . $row['mid'] . "</td>";
            echo "<td>" . $row['assignment'] . "</td>";
            echo "<td>" . $row['attendance'] . "</td>";
            echo "<td>" . $row['final'] . "</td>";
            
            // Calculate total mark for this row
            $total_marks = $row['quiz'] + $row['mid'] + $row['assignment'] + $row['attendance'] + $row['final'];
            echo "<td>" . $total_marks . "</td>";
            
            echo "</tr>";
        }

        echo "</table>";
    }

    // Close database connection
    mysqli_close($con);
    ?>
</div>
<div class="container" style="color: white;">
    <a href="student_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
</div>
</body>
</html>
