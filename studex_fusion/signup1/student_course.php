<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Courses</title>
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
        <h1>Enroll in Courses</h1>
        <?php
        session_start();

        // Include the database connection file
        include 'connect.php';

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: student_login.php"); // Redirect to login page if not logged in
            exit();
        }

        // Get username from session
        $username = $_SESSION["username"];

        // Query to fetch student's st_id from the student table using the username
        $sql = "SELECT st_id FROM student WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch student's st_id
            $studentData = $result->fetch_assoc();
            $st_id = $studentData['st_id'];

            if (isset($_POST['enroll'])) {
                $selected_course = $_POST['selected_course'];

                // Check if there are available seats for the selected course
                $sql_check_seats = "SELECT seat FROM available_courses WHERE course_code = ? AND seat > 0";
                $stmt_check_seats = $con->prepare($sql_check_seats);
                $stmt_check_seats->bind_param("s", $selected_course);
                $stmt_check_seats->execute();
                $result_check_seats = $stmt_check_seats->get_result();

                if ($result_check_seats->num_rows > 0) {
                    // Decrement seat count for the selected course
                    $sql_update_seats = "UPDATE available_courses SET seat = seat - 1 WHERE course_code = ?";
                    $stmt_update_seats = $con->prepare($sql_update_seats);
                    $stmt_update_seats->bind_param("s", $selected_course);
                    $stmt_update_seats->execute();

                    // Insert into course table
                    $sql_insert_course = "INSERT INTO course (st_id, course_code) VALUES (?, ?)";
                    $stmt_insert_course = $con->prepare($sql_insert_course);
                    $stmt_insert_course->bind_param("is", $st_id, $selected_course);
                    $stmt_insert_course->execute();

                    // Insert into mark table with default marks
                    $sql_insert_mark = "INSERT INTO mark (st_id, course_code, quiz, assignment, mid, final, attendance) 
                                       VALUES (?, ?, 0, 0, 0, 0, 0)";
                    $stmt_insert_mark = $con->prepare($sql_insert_mark);
                    $stmt_insert_mark->bind_param("is", $st_id, $selected_course);
                    $stmt_insert_mark->execute();

                    echo "You have successfully enrolled in course: " . $selected_course;
                } else {
                    echo "No available seats for the selected course.";
                }
            }

            // If drop button is clicked
            if (isset($_POST['drop'])) {
                $dropped_course = $_POST['dropped_course'];

                // Update seat count for the dropped course
                $sql_update_seats = "UPDATE available_courses SET seat = seat + 1 WHERE course_code = ?";
                $stmt_update_seats = $con->prepare($sql_update_seats);
                $stmt_update_seats->bind_param("s", $dropped_course);
                $stmt_update_seats->execute();

                // Delete the dropped course from the course table
                $sql_delete_course = "DELETE FROM course WHERE st_id = ? AND course_code = ?";
                $stmt_delete_course = $con->prepare($sql_delete_course);
                $stmt_delete_course->bind_param("is", $st_id, $dropped_course);
                $stmt_delete_course->execute();

                // Delete the dropped course from the mark table
                $sql_delete_mark = "DELETE FROM mark WHERE st_id = ? AND course_code = ?";
                $stmt_delete_mark = $con->prepare($sql_delete_mark);
                $stmt_delete_mark->bind_param("is", $st_id, $dropped_course);
                $stmt_delete_mark->execute();

                echo "You have successfully dropped course: " . $dropped_course;
            }

            // Query to check if the student already has 4 courses enrolled
            $sql_courses = "SELECT COUNT(*) AS num_courses FROM course WHERE st_id = ?";
            $stmt_courses = $con->prepare($sql_courses);
            $stmt_courses->bind_param("i", $st_id);
            $stmt_courses->execute();
            $result_courses = $stmt_courses->get_result();

            if ($result_courses->num_rows > 0) {
                $course_count = $result_courses->fetch_assoc()['num_courses'];

                if ($course_count >= 4) {
                    echo "No course available this semester. You have already enrolled in 4 courses.";
                } else {
                    // Student is eligible to enroll in courses
                    echo "Eligible to enroll.<br>";
                    echo "Select a course to enroll in:<br>";

                    // Query to fetch available courses with available seats
                    $sql_available_courses = "SELECT * FROM available_courses 
                                              WHERE course_code NOT IN 
                                              (SELECT course_code FROM course WHERE st_id = ?) 
                                              AND seat > 0";
                    $stmt_available_courses = $con->prepare($sql_available_courses);
                    $stmt_available_courses->bind_param("i", $st_id);
                    $stmt_available_courses->execute();
                    $result_available_courses = $stmt_available_courses->get_result();

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
                        echo "No available courses with open seats.";
                    }
                }
            }
        }

        // Query to fetch enrolled courses for the student
        $sql_enrolled_courses = "SELECT c.course_code, ac.name FROM course c
                                 JOIN available_courses ac ON c.course_code = ac.course_code
                                 WHERE c.st_id = ?";
        $stmt_enrolled_courses = $con->prepare($sql_enrolled_courses);
        $stmt_enrolled_courses->bind_param("i", $st_id);
        $stmt_enrolled_courses->execute();
        $result_enrolled_courses = $stmt_enrolled_courses->get_result();

        if ($result_enrolled_courses->num_rows > 0) {
            echo "<div class='enrolled-courses'>";
            echo "<h2>Enrolled Courses:</h2>";
            echo "<table>";
            echo "<tr><th>Course Code</th><th>Course Name</th><th>Action</th></tr>";
            while ($row = $result_enrolled_courses->fetch_assoc()) {
                echo "<tr><td>" . $row['course_code'] . "</td><td>" . $row['name'] . "</td>";
                echo "<td><form method='post'><input type='hidden' name='dropped_course' value='" . $row['course_code'] . "'>";
                echo "<input type='submit' name='drop' value='Drop'></form></td></tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='enrolled-courses'>";
            echo "<h2>Enrolled Courses:</h2>";
            echo "<p>No courses enrolled.</p>";
            echo "</div>";
        }

        // Close the database connection
        $con->close();
        
        ?>
    </div>
    <!-- Add this at the end of the body -->
    <div class="container" style="color: white;">
        <a href="student_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>

</body>
</html>
