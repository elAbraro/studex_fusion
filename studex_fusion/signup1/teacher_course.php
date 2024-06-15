<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll as Faculty</title>
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
        .course {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #1f2d3d;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .course-name {
            flex: 1;
            color: #fff;
        }
        .course-select {
            margin-left: 20px;
        }
        select {
            padding: 8px;
            font-size: 16px;
            color: black; /* Fix the color issue */
            border: none;
            border-radius: 5px;
            background-color: #fff;
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
        .enrolled-courses {
            margin-top: 30px;
        }
        .enrolled-courses h2 {
            margin-bottom: 10px;
            text-align: center;
        }
        .enrolled-courses table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #fff; /* Add border */
        }
        .enrolled-courses th, .enrolled-courses td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #fff;
        }
        .enrolled-courses th {
            background-color: #f2f2f2;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enroll as Faculty</h1>
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
        $unique_int = $_SESSION["unique_int"]; // Assuming the unique_int is stored in the session as the username
        
        // Query to check if the teacher already has 2 courses enrolled
        $sql_courses = "SELECT COUNT(*) AS num_courses FROM t_course WHERE unique_int = ?";
        $stmt_courses = $con->prepare($sql_courses);
        $stmt_courses->bind_param("s", $unique_int);
        $stmt_courses->execute();
        $result_courses = $stmt_courses->get_result();

        if ($result_courses->num_rows > 0) {
            $course_count = $result_courses->fetch_assoc()['num_courses'];

            if ($course_count >= 2) {
                echo "You have already enrolled in 3 courses as faculty.";
            } else {
                // Teacher is eligible to enroll as faculty
                echo "Eligible to enroll<br>";
                echo "Select a course to enroll as faculty:<br>";

                // Query to fetch available courses without faculty
                $sql_available_courses = "SELECT * FROM available_courses 
                                          WHERE course_code NOT IN 
                                          (SELECT course_code FROM t_course) 
                                          AND seat > 0";
                $result_available_courses = $con->query($sql_available_courses);

                if ($result_available_courses->num_rows > 0) {
                    // Display available courses
                    echo "<form method='post'>";
                    while ($row = $result_available_courses->fetch_assoc()) {
                        echo "<div class='course'>";
                        echo "<div class='course-name'>" . $row['name'] . " - " . $row['course_code'] . "</div>";
                        echo "<div class='course-select'><input type='radio' name='selected_course' value='" . $row['course_code'] . "'></div>";
                        echo "</div>";
                    }

                    echo "<input type='submit' name='enroll' value='Enroll'>";
                    echo "</form>";
                } else {
                    echo "No available courses without faculty.";
                }
            }
        }

        // If enroll button is clicked
        if (isset($_POST['enroll'])) {
            $selected_course = $_POST['selected_course'];

            // Query to check if there is no existing faculty for the selected course
            $sql_check_faculty = "SELECT * FROM t_course WHERE course_code = ?";
            $stmt_check_faculty = $con->prepare($sql_check_faculty);
            $stmt_check_faculty->bind_param("s", $selected_course);
            $stmt_check_faculty->execute();
            $result_check_faculty = $stmt_check_faculty->get_result();

            if ($result_check_faculty->num_rows == 0) {
                // Insert into t_course table
                $sql_insert_course = "INSERT INTO t_course (unique_int, course_code) VALUES (?, ?)";
                $stmt_insert_course = $con->prepare($sql_insert_course);
                $stmt_insert_course->bind_param("ss", $unique_int, $selected_course);
                $stmt_insert_course->execute();

                echo "You have successfully enrolled as faculty in course: " . $selected_course;
            } else {
                echo "This course already has a faculty.";
            }
        }

        // If drop button is clicked
        if (isset($_POST['drop'])) {
            $dropped_course = $_POST['dropped_course'];

            // Delete from t_course table
            $sql_drop_course = "DELETE FROM t_course WHERE unique_int = ? AND course_code = ?";
            $stmt_drop_course = $con->prepare($sql_drop_course);
            $stmt_drop_course->bind_param("ss", $unique_int, $dropped_course);
            $stmt_drop_course->execute();

            echo "You have successfully dropped course: " . $dropped_course;
        }

        // Query to fetch courses the teacher is taking as faculty
        $sql_enrolled_courses = "SELECT tc.course_code, ac.name FROM t_course tc
                                 JOIN available_courses ac ON tc.course_code = ac.course_code
                                 WHERE tc.unique_int = ?";
        $stmt_enrolled_courses = $con->prepare($sql_enrolled_courses);
        $stmt_enrolled_courses->bind_param("s", $unique_int);
        $stmt_enrolled_courses->execute();
        $result_enrolled_courses = $stmt_enrolled_courses->get_result();

        if ($result_enrolled_courses->num_rows > 0) {
            echo "<div class='enrolled-courses'>";
            echo "<h2>Enrolled Courses as Faculty:</h2>";
            echo "<table>";
            echo "<tr><th>Course Code</th><th>Course Name</th><th>Action</th></tr>";
            while ($row = $result_enrolled_courses->fetch_assoc()) {
                echo "<tr><td>" . $row['course_code'] . "</td><td>" . $row['name'] . "</td>";
                echo "<td>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='dropped_course' value='" . $row['course_code'] . "'>";
                echo "<input type='submit' name='drop' value='Drop'>";
                echo "</form>";
                echo "</td></tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='enrolled-courses'>";
            echo "<h2>Enrolled Courses as Faculty:</h2>";
            echo "<p>No courses enrolled as faculty.</p>";
            echo "</div>";
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
