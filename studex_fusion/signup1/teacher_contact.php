<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Contacts</title>
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
        <h1>Teacher Contacts</h1>
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

        // Query to fetch teacher's unique_int from the teacher table using the username
        $sql = "SELECT unique_int FROM teacher WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch teacher's unique_int
            $teacherData = $result->fetch_assoc();
            $unique_int = $teacherData['unique_int'];

            // Query to fetch course codes associated with the teacher's unique_int
            $sql_courses = "SELECT course_code FROM t_course WHERE unique_int = ?";
            $stmt_courses = $con->prepare($sql_courses);
            $stmt_courses->bind_param("s", $unique_int);
            $stmt_courses->execute();
            $result_courses = $stmt_courses->get_result();

            if ($result_courses->num_rows > 0) {
                echo "<div class='enrolled-courses'>";
                echo "<h2>Enrolled Courses:</h2>";
                echo "<table>";
                echo "<tr><th>Course Code</th><th>Student ID</th><th>Student Name</th><th>Student Email</th></tr>";
                
                // Fetching course codes associated with the teacher's unique_int
                while ($row = $result_courses->fetch_assoc()) {
                    $course_code = $row['course_code'];

                    // Query to fetch student information for each course code
                    $sql_students = "SELECT s.st_id, s.name, s.mail FROM student s
                                    JOIN course c ON s.st_id = c.st_id
                                    WHERE c.course_code = ?";
                    $stmt_students = $con->prepare($sql_students);
                    $stmt_students->bind_param("s", $course_code);
                    $stmt_students->execute();
                    $result_students = $stmt_students->get_result();

                    // Displaying student information
                    while ($student_row = $result_students->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $course_code . "</td>";
                        echo "<td>" . $student_row['st_id'] . "</td>";
                        echo "<td>" . $student_row['name'] . "</td>";
                        echo "<td>" . $student_row['mail'] . "</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>No courses enrolled.</p>";
            }
        }


        // Close the database connection
        $con->close();
        ?>
    </div>
    <!-- Add this at the end of the body -->
    <div class="container" style="color: white;">
        <a href="teacher_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>
</body>
</html>
