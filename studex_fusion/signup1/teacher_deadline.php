<?php
session_start();

// Include the database connection file
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: teacher_login.php"); // Redirect to login page if not logged in
    exit();
}

// Get username from session
$username = $_SESSION["username"];
$unique_int= $_SESSION['unique_int'];
// Function to validate date and time format
function isValidDateTime($dateTime)
{
    $dateTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
    return $dateTimeObj !== false && $dateTimeObj !== null && $dateTimeObj->format('Y-m-d H:i:s') === $dateTime;
}


// Function to remove past deadlines
function removePastDeadlines($con)
{
    $sql_remove_past = "DELETE FROM deadline WHERE date < NOW()";
    $con->query($sql_remove_past);
}

function fetchEnrolledCourses($con, $unique_int) {
    $sql_enrolled_courses = "SELECT course_code FROM t_course WHERE unique_int = ?";
    $stmt_enrolled_courses = $con->prepare($sql_enrolled_courses);
    $stmt_enrolled_courses->bind_param("s", $unique_int);
    $stmt_enrolled_courses->execute();
    $result_enrolled_courses = $stmt_enrolled_courses->get_result();
    
    return $result_enrolled_courses;
}


// Function to fetch existing deadlines for the teacher's enrolled courses
function fetchExistingDeadlines($con, $username)
{
    $sql_enrolled_courses = "SELECT course_code FROM t_course WHERE unique_int = 
        (SELECT unique_int FROM teacher WHERE username = ?)";
    $stmt_enrolled_courses = $con->prepare($sql_enrolled_courses);
    $stmt_enrolled_courses->bind_param("s", $username);
    $stmt_enrolled_courses->execute();
    $result_enrolled_courses = $stmt_enrolled_courses->get_result();

    if ($result_enrolled_courses->num_rows > 0) {
        // Array to store course codes
        $courseCodes = [];
        while ($row = $result_enrolled_courses->fetch_assoc()) {
            $courseCodes[] = "'" . $row['course_code'] . "'";
        }
        $courseCodesString = implode(",", $courseCodes);

        // Query to fetch existing deadlines for the teacher's enrolled courses
        $sql_existing_deadlines = "SELECT * FROM deadline WHERE course_code IN ($courseCodesString)";
        $result_existing_deadlines = $con->query($sql_existing_deadlines);

        // Display existing deadlines in a table
        if ($result_existing_deadlines->num_rows > 0) {
            while ($row = $result_existing_deadlines->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['course_code'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                // Format the date and time to match PHPMyAdmin's datetime format
                echo "<td>" . date('Y-m-d', strtotime($row['date'])) . "</td>";
                echo "<td>" . date('H:i:s', strtotime($row['time'])) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No existing deadlines</td></tr>";
        }
    } else {
        echo "<tr><td colspan='4'>You are not enrolled in any courses</td></tr>";
    }
}

// Process form submission
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"])) {
        $courseCode = $_POST["course_code"];
        $type = $_POST["type"];
        $date = $_POST["date"];
        $time = $_POST["time"];

        // Check if the teacher is enrolled in the specified course
        $sql_check_enrollment = "SELECT * FROM t_course WHERE unique_int = 
            (SELECT unique_int FROM teacher WHERE username = ?) AND course_code = ?";
        $stmt_check_enrollment = $con->prepare($sql_check_enrollment);
        $stmt_check_enrollment->bind_param("ss", $username, $courseCode);
        $stmt_check_enrollment->execute();
        $result_check_enrollment = $stmt_check_enrollment->get_result();

        if ($result_check_enrollment->num_rows > 0) {
            // Check if the deadline type already exists for the specified course
            $sql_check_deadline = "SELECT * FROM deadline WHERE course_code = ? AND type = ?";
            $stmt_check_deadline = $con->prepare($sql_check_deadline);
            $stmt_check_deadline->bind_param("ss", $courseCode, $type);
            $stmt_check_deadline->execute();
            $result_check_deadline = $stmt_check_deadline->get_result();

            if ($result_check_deadline->num_rows == 0) {
                // Check if the date and time format is valid
                if (isValidDateTime("$date $time")) {
                    // Insert new deadline into the database
                    $sql_insert_deadline = "INSERT INTO deadline (course_code, type, date, time) 
                        VALUES (?, ?, ?, ?)";
                    $stmt_insert_deadline = $con->prepare($sql_insert_deadline);
                    $stmt_insert_deadline->bind_param("ssss", $courseCode, $type, $date, $time);
                    $stmt_insert_deadline->execute();

                    echo "<script>alert('Deadline added successfully');</script>";
                } else {
                    echo "<script>alert('Invalid date or time format');</script>";
                }
            } else {
                echo "<script>alert('Deadline type already exists for this course');</script>";
            }
        } else {
            echo "<script>alert('You are not enrolled in the specified course');</script>";
        }
    }
}


// Remove past deadlines
removePastDeadlines($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Deadline</title>
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
        form {
            text-align: center;
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
            color: #fff;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        label {
            display: inline-block;
            width: 120px;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            padding: 8px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            width: calc(100% - 110px); /* Adjust width to accommodate label width */
            margin-bottom: 10px;
            margin-left: 10px; /* Add left margin to align with label */
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Deadline</h1>
        <form method="post">
            <label for="course_code">Course Code:</label>
            <input type="text" id="course_code" name="course_code" required><br><br>
            
            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="attendance">Attendance</option>
                <option value="quiz">Quiz</option>
                <option value="mid">Mid</option>
                <option value="final">Final</option>
                <option value="assignment">Assignment</option>
            </select><br><br>
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br><br>
            
            <label for="time">Time:</label>
            <input type="time" id="time" name="time" step='1' required><br><br>
            
            <input type="submit" name="submit" value="Add Deadline">
        </form>
        
        <h2>Existing Deadlines</h2>
        <table>
            <tr>
                <th>Course Code</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
            <?php
            // Display existing deadlines for the teacher's enrolled courses
            fetchExistingDeadlines($con, $username);
            ?>
        </table>
        <h2>Your Enrolled Courses</h2>
        <table>
            <tr>
                <th>Course Code</th>
            </tr>
            <?php
            $enrolled_courses_result = fetchEnrolledCourses($con, $unique_int);
            if ($enrolled_courses_result->num_rows > 0) {
                while ($row = $enrolled_courses_result->fetch_assoc()) {
                    echo "<tr><td>" . $row['course_code'] . "</td></tr>";
                }
            } else {
                echo "<tr><td>No courses found</td></tr>";
            }
            ?>
    </div>
    <div class="container" style="color: white;">
        <a href="teacher_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>
</body>
</html>
