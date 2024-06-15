<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['username'])) {
    header("Location: student_login.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection file
include_once "connect.php";

// Fetch student ID from session
$username = $_SESSION['username'];
$st_id_query = "SELECT st_id FROM student WHERE username = ?";
$stmt_st_id = $con->prepare($st_id_query);
$stmt_st_id->bind_param("s", $username);
$stmt_st_id->execute();
$st_id_result = $stmt_st_id->get_result();
$st_id_row = $st_id_result->fetch_assoc();
$st_id = $st_id_row['st_id'];

// Query to fetch course codes for the student in which the student is enrolled
$course_query = "SELECT course_code FROM course WHERE st_id = ?";
$stmt_course = $con->prepare($course_query);
$stmt_course->bind_param("i", $st_id);
$stmt_course->execute();
$course_result = $stmt_course->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Courses</title>
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
        <h1>Enrolled Courses</h1>
        <table>
            <tr>
                <th>Course Code</th>
                <th>Teacher Name</th>
                <th>Email</th>
            </tr>
            <?php
            while ($course_row = $course_result->fetch_assoc()) {
                $course_code = $course_row['course_code'];

                // Query to fetch teacher name and email using course_code from teacher table
                $teacher_query = "SELECT t.name AS teacher_name, t.mail AS teacher_mail
                                  FROM teacher t
                                  INNER JOIN t_course tc ON t.unique_int = tc.unique_int
                                  WHERE tc.course_code = ?";
                $stmt_teacher = $con->prepare($teacher_query);
                $stmt_teacher->bind_param("s", $course_code);
                $stmt_teacher->execute();
                $teacher_result = $stmt_teacher->get_result();

                // Display course information with fallback for unavailable teacher info
                if ($teacher_result->num_rows > 0) {
                    $teacher_row = $teacher_result->fetch_assoc();
                    $teacher_name = $teacher_row['teacher_name'];
                    $teacher_email = $teacher_row['teacher_mail'];
                } else {
                    // If no teacher is enrolled in the course, display info not available
                    $teacher_name = "Info not available";
                    $teacher_email = "Info not available";
                }

                echo "<tr>";
                echo "<td>$course_code</td>";
                echo "<td>$teacher_name</td>";
                echo "<td>$teacher_email</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div class="container" style="color: white;">
        <a href="student_home.php" style="color: white; text-decoration: none;">Dashboard</a>
    </div>
</body>
</html>
