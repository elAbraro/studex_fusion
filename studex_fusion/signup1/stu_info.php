<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
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
        .total-credit {
            font-weight: bold;
            color: #ffc107; /* Yellow color for total credit */
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
        <h1>Student Information</h1>
        <?php
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: student_login.php"); // Redirect to login page if not logged in
            exit();
        }

        // Include the database connection file
        include 'connect.php';

        // Get username from session
        $username = ($_SESSION["username"]);

        // Query to fetch user's data from the student table
        $sql = "SELECT * FROM student WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch user's data and store it in $userData array
            $userData = $result->fetch_assoc();

            // Display user data in a table
            echo "<table>";
            foreach ($userData as $key => $value) {
                echo "<tr>";
                echo "<th>$key</th>";
                echo "<td>$value</td>";
                echo "</tr>";
            }
            $totalCredit = $userData["course_count"] * 3;
            echo "<tr class='total-credit'><th>Total Credit</th><td>$totalCredit</td></tr>";
            echo "</table>";
        } else {
            echo "No data found for the user.";
        }

        // Close the database connection
        $con->close();
        ?>
    </div>
    <div class="container" style="color: white;">
        <a href="student_home.php" style="color: white; text-decoration: none;"> Dashboard</a>
    </div>
</body>
</html>
