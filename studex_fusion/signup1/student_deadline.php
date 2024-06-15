<?php
session_start();

// Include the database connection file
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: student_login.php"); // Redirect to login page if not logged in
    exit();
}

// Get student username from session
$username = $_SESSION["username"];

// Fetch student ID from database
$sql_st_id = "SELECT st_id FROM student WHERE username = ?";
$stmt_st_id = $con->prepare($sql_st_id);
$stmt_st_id->bind_param("s", $username);
$stmt_st_id->execute();
$result_st_id = $stmt_st_id->get_result();

// Check if the student exists
if ($result_st_id->num_rows > 0) {
    $row = $result_st_id->fetch_assoc();
    $st_id = $row['st_id'];
    // Store student ID in session
    $_SESSION["st_id"] = $st_id;
} else {
    // If student does not exist, redirect to login page
    header("Location: student_login.php");
    exit();
}

// Function to retrieve courses associated with the student
function getCourses($con, $st_id)
{
    $sql_courses = "SELECT course_code FROM course WHERE st_id = ?";
    $stmt_courses = $con->prepare($sql_courses);
    $stmt_courses->bind_param("i", $st_id);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();

    $courses = [];
    while ($row = $result_courses->fetch_assoc()) {
        $courses[] = "'" . $row['course_code'] . "'";
    }
    return $courses;
}

// Function to fetch deadlines for the student's courses
function fetchStudentDeadlines($con, $courses)
{
    if (empty($courses)) {
        echo "<tr><td colspan='4'>You are not enrolled in any courses</td></tr>";
        return;
    }

    $courseCodesString = implode(",", $courses);
    $currentDateTime = date("Y-m-d H:i:s");

    // Query to fetch deadlines for the student's courses
    $sql_deadlines = "SELECT * FROM deadline WHERE course_code IN ($courseCodesString) AND CONCAT(date, ' ', time) >= ? ORDER BY CONCAT(date, ' ', time) ASC";
    $stmt_deadlines = $con->prepare($sql_deadlines);
    $stmt_deadlines->bind_param("s", $currentDateTime);
    $stmt_deadlines->execute();
    $result_deadlines = $stmt_deadlines->get_result();

    if ($result_deadlines->num_rows > 0) {
        while ($row = $result_deadlines->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['course_code'] . "</td>";
            echo "<td>" . $row['type'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . $row['time'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No upcoming deadlines</td></tr>";
    }
}

// Retrieve courses associated with the student
$courses = getCourses($con, $st_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Deadlines</title>
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
            color: #fff;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upcoming Deadlines</h1>
        <table>
            <tr>
                <th>Course Code</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
            <?php
            // Fetch and display deadlines for the student's courses
            fetchStudentDeadlines($con, $courses);
            ?>
        </table>
    </div>
    <div class="container" style="color: white;">
        <a href="student_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>
</body>
</html>
