<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Marks</title>
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
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        td {
            color: #fff;
        }
        .remove-button {
            background-color: #ff3333;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Student Marks</h1>
        <?php
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: teacher_login.php"); // Redirect to login page if not logged in
            exit();
        }

        // Include the database connection file
        include 'connect.php';

        // Get unique_int from session using username
        $username = $_SESSION["username"];
        $unique_int_query = "SELECT unique_int FROM teacher WHERE username = ?";
        $stmt_unique_int = $con->prepare($unique_int_query);
        $stmt_unique_int->bind_param("s", $username);
        $stmt_unique_int->execute();
        $unique_int_result = $stmt_unique_int->get_result();
        $unique_int_row = $unique_int_result->fetch_assoc();
        $unique_int = $unique_int_row['unique_int'];

        // Query to fetch course codes the teacher is enrolled into
        $course_query = "SELECT course_code FROM t_course WHERE unique_int = ?";
        $stmt_course = $con->prepare($course_query);
        $stmt_course->bind_param("s", $unique_int);
        $stmt_course->execute();
        $course_result = $stmt_course->get_result();

        // Display form to select course code and student information
        echo "<form method='post'>";
        echo "Select Course Code: ";
        echo "<select name='course_code'>";

        // Prepare a query to fetch course codes that the teacher is enrolled in
        $enrolled_course_query = "SELECT course_code FROM t_course WHERE unique_int = ?";
        $stmt_enrolled_course = $con->prepare($enrolled_course_query);
        $stmt_enrolled_course->bind_param("s", $unique_int);
        $stmt_enrolled_course->execute();
        $enrolled_course_result = $stmt_enrolled_course->get_result();

        // Fetch all course codes the teacher is enrolled in
        $enrolled_course_codes = array();
        while ($row = $enrolled_course_result->fetch_assoc()) {
            $enrolled_course_codes[] = $row['course_code'];
        }

        // Prepare a query to fetch all distinct course codes
        $all_course_query = "SELECT DISTINCT course_code FROM t_course";
        $stmt_all_course = $con->prepare($all_course_query);
        $stmt_all_course->execute();
        $all_course_result = $stmt_all_course->get_result();

        // Loop through all course codes and display only the enrolled ones
        while ($row = $all_course_result->fetch_assoc()) {
            $course_code = $row['course_code'];
            if (in_array($course_code, $enrolled_course_codes)) {
                echo "<option value='" . $course_code . "'>" . $course_code . "</option>";
            }
        }

        echo "</select>";
        echo "<br><br>";
        echo "Select Student ID: ";
        echo "<input type='text' name='st_id'>";
        echo "<br><br>";
        echo "Select Mark Type: ";
        echo "<select name='mark_type'>";
        echo "<option value='quiz'>Quiz</option>";
        echo "<option value='mid'>Mid</option>";
        echo "<option value='assignment'>Assignment</option>";
        echo "<option value='attendance'>Attendance</option>";
        echo "<option value='final'>Final</option>";
        echo "</select>";
        echo "<br><br>";
        echo "Enter Mark: ";
        echo "<input type='text' name='mark'>";
        echo "<br><br>";
        echo "<input type='submit' name='submit' value='Submit'>";
        echo "</form>";

        // Handle form submission for updating marks
        if (isset($_POST['submit'])) {
            $course_code = $_POST['course_code'];
            $st_id = $_POST['st_id'];
            $mark_type = $_POST['mark_type'];
            $mark = $_POST['mark'];

            // Check if the teacher is enrolled in the selected course
            $enrollment_query = "SELECT COUNT(*) AS enrolled FROM t_course WHERE unique_int = ? AND course_code = ?";
            $stmt_enrollment = $con->prepare($enrollment_query);
            $stmt_enrollment->bind_param("is", $unique_int, $course_code);
            $stmt_enrollment->execute();
            $enrollment_result = $stmt_enrollment->get_result();
            $enrollment_row = $enrollment_result->fetch_assoc();
            $enrolled = $enrollment_row['enrolled'];

            if ($enrolled > 0) {
                // Check if the student is enrolled in the selected course
                $student_enrollment_query = "SELECT COUNT(*) AS enrolled FROM course WHERE st_id = ? AND course_code = ?";
                $stmt_student_enrollment = $con->prepare($student_enrollment_query);
                $stmt_student_enrollment->bind_param("is", $st_id, $course_code);
                $stmt_student_enrollment->execute();
                $student_enrollment_result = $stmt_student_enrollment->get_result();
                $student_enrollment_row = $student_enrollment_result->fetch_assoc();
                $student_enrolled = $student_enrollment_row['enrolled'];

                if ($student_enrolled > 0) {
                    // Query to update the mark for the specified course and student
                    $update_query = "UPDATE mark SET $mark_type = ? WHERE st_id = ? AND course_code = ?";
                    $stmt_update = $con->prepare($update_query);
                    $stmt_update->bind_param("dis", $mark, $st_id, $course_code);
                    $stmt_update->execute();

                    echo "Mark updated successfully!";
                } else {
                    echo "The student is not enrolled in the selected course code.";
                }
            } else {
                echo "You are not enrolled in the selected course code.";
            }
        }

        // Query to fetch marks for all students in the selected course
        if (isset($_POST['course_code'])) {
            $selected_course_code = $_POST['course_code'];
            $marks_query = "SELECT * FROM mark WHERE course_code = ?";
            $stmt_marks = $con->prepare($marks_query);
            $stmt_marks->bind_param("s", $selected_course_code);
            $stmt_marks->execute();
            $marks_result = $stmt_marks->get_result();

            // Display marks in a table
            echo "<h1>Student Marks for Course Code: $selected_course_code</h1>";
            echo "<table>
                    <tr>
                        <th>Student ID</th>
                        <th>Quiz</th>
                        <th>Mid</th>
                        <th>Assignment</th>
                        <th>Attendance</th>
                        <th>Final</th>
                        <th>Actions</th>
                    </tr>";
            while ($row = $marks_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['st_id'] . "</td>";
                echo "<td>" . $row['quiz'] . "</td>";
                echo "<td>" . $row['mid'] . "</td>";
                echo "<td>" . $row['assignment'] . "</td>";
                echo "<td>" . $row['attendance'] . "</td>";
                echo "<td>" . $row['final'] . "</td>";
                echo "<td><form method='post'>";
                echo "<input type='hidden' name='remove_st_id' value='" . $row['st_id'] . "'>";
                echo "<input type='hidden' name='remove_course_code' value='" . $selected_course_code . "'>";
                echo "<input type='submit' class='remove-button' name='remove' value='Remove Mark'>";
                echo "</form></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        

        // Handle removal of marks
        if (isset($_POST['remove'])) {
            $remove_st_id = $_POST['remove_st_id'];
            $remove_course_code = $_POST['remove_course_code'];
            $remove_query = "DELETE FROM mark WHERE st_id = ? AND course_code = ?";
            $stmt_remove = $con->prepare($remove_query);
            $stmt_remove->bind_param("is", $remove_st_id, $remove_course_code);
            $stmt_remove->execute();
            echo "Mark removed successfully!";
        }

        // Close the database connection
        $con->close();
        ?>
    </div>
    <div class="container" style="color: white;">
        <a href="teacher_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>
     
</body>
</html>
