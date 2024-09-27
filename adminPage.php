<?php
// Check if the request method is POST and if the action is to delete subjects
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] === "delete_subjects") {
    // Retrieve the selected subjects array from the request
    if (isset($_POST["selected_subjects"])) {
        $selectedSubjectsArray = json_decode($_POST["selected_subjects"], true);

        // Establish connection to the database
        $conn = mysqli_connect("localhost", "root", "", "students");

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepare the delete statement
        $delete_query = "DELETE FROM Failed_Subjects WHERE Failed_Subject_ID = ?";
        $stmt = mysqli_prepare($conn, $delete_query);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'i', $subjectID);

        // Loop through the selected subjects array and delete each subject
        foreach ($selectedSubjectsArray as $subjectID) {
            // Execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Subject deleted successfully
                // You can log this or perform any additional actions
            } else {
                // Error occurred while deleting subject
                // Log the error or handle it as needed
                error_log("Error deleting subject with ID $subjectID: " . mysqli_error($conn));
            }
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        // Close database connection
        mysqli_close($conn);

        // Redirect to the same page after deleting subjects
        header("Location: {$_SERVER['PHP_SELF']}");
        exit; // Terminate PHP execution after handling the request
    } else {
        // Handle missing or invalid data in the request
        echo "Invalid data received.";
        exit; // Terminate PHP execution after handling the request
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="adminPage.css">

</head>

<body>

    <header>
        <!-- Logo -->
        <div class="logo">
            <img src="./image/collegeLogo.png" alt="Logo">
        </div>

        <!-- Search input field with button -->
        <div class="search-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" id="search_registration_number" name="search_registration_number"
                    placeholder="Search by Registration Number">
                <input type="submit" value="Search">
            </form>
        </div>

        <!-- Buttons -->
        <form id="deleteSubjectsForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="selected_subjects" id="selectedSubjectsInput">
            <input type="hidden" name="action" value="delete_subjects">
            <input type="button" class="header-button danger" value="Delete" onclick="deleteSelectedSubjects()">
        </form>

        <!-- <button class="header-button primary">Update</button> -->
        <button class="header-button primary" onclick="showAddForm()">Add</button>
        <a href="https://sepolia.etherscan.io/" target="_blank"><button class="header-button primary">Check
                Transaction</button></a>
    </header>

    <!-- Content for retrieve.php -->
    <div id="addForm" style="display:none;">
        <h2>Insert Student Details</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="insert_form">
            <label for="insert_registration_number">Registration Number:</label>
            <input type="text" id="insert_registration_number" name="registration_number" required>

            <label for="insert_name">Student Name:</label>
            <input type="text" id="insert_name" name="name" required>

            <label for="insert_branch">Student Branch:</label>
            <input type="text" id="insert_branch" name="branch" required>

            <label for="insert_course">Student Course:</label>
            <input type="text" id="insert_course" name="course" required>

            <label for="insert_subject_name">Subject Name:</label>
            <input type="text" id="insert_subject_name" name="subject_name" required>

            <label for="insert_subject_code">Subject Code:</label>
            <input type="text" id="insert_subject_code" name="subject_code" required>

            <!-- New input fields for rupees and ethers -->
            <label for="insert_amount_rupees">Amount in Rupees:</label>
            <input type="text" id="insert_amount_rupees" name="amount_rupees">

            <label for="insert_amount_ethers">Amount in Ethers:</label>
            <input type="text" id="insert_amount_ethers" name="amount_ethers">

            <input type="submit" value="Submit">
        </form>

        <?php
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if all form fields are set and not empty
            if (
                isset($_POST['registration_number']) && !empty($_POST['registration_number']) &&
                isset($_POST['name']) && !empty($_POST['name']) &&
                isset($_POST['branch']) && !empty($_POST['branch']) &&
                isset($_POST['course']) && !empty($_POST['course']) &&
                isset($_POST['subject_name']) && !empty($_POST['subject_name']) &&
                isset($_POST['subject_code']) && !empty($_POST['subject_code']) &&
                isset($_POST['amount_rupees']) && !empty($_POST['amount_rupees']) &&
                isset($_POST['amount_ethers']) && !empty($_POST['amount_ethers'])
            ) {

                // Retrieve form data
                $registration_number = $_POST['registration_number'];
                $name = $_POST['name'];
                $branch = $_POST['branch'];
                $course = $_POST['course'];
                $subject_name = $_POST['subject_name'];
                $subject_code = $_POST['subject_code'];
                $amount_rupees = $_POST['amount_rupees'];
                $amount_ethers = $_POST['amount_ethers'];

                // Establish database connection
                $conn = mysqli_connect("localhost", "root", "", "students");

                // Check connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Check if the student already exists based on registration number
                $check_student_query = "SELECT * FROM Students WHERE Registration_Number = '$registration_number'";
                $check_student_result = mysqli_query($conn, $check_student_query);

                if (mysqli_num_rows($check_student_result) > 0) {
                    // Student already exists, check if the name matches
                    $existing_student = mysqli_fetch_assoc($check_student_result);
                    if ($existing_student['Name'] !== $name) {
                        // Different name for existing registration number, show error
                        echo "<p>Error: A student with the same registration number already exists but with a different name.</p>";
                        exit; // Stop further execution
                    }
                    // Get the Student_ID of the existing student
                    $student_id = $existing_student['Student_ID'];
                } else {
                    // Student doesn't exist, insert new student
                    $insert_student_query = "INSERT INTO Students (Registration_Number, Name,branch,course) VALUES ('$registration_number', '$name','$branch','$course')";
                    mysqli_query($conn, $insert_student_query);
                    // Get the Student_ID of the newly inserted student
                    $student_id = mysqli_insert_id($conn);
                }

                // Check if the subject already exists for this student
                $check_subject_query = "SELECT * FROM Failed_Subjects WHERE Student_ID = $student_id AND Subject_Code = '$subject_code'";
                $check_subject_result = mysqli_query($conn, $check_subject_query);

                if (mysqli_num_rows($check_subject_result) > 0) {
                    // Subject already exists for this student, show error
                    echo "<p>Error: This subject already exists for this student.</p>";
                    exit; // Stop further execution
                }

                // Insert failed subject details into Failed_Subjects table
                $insert_failed_subject_query = "INSERT INTO Failed_Subjects (Student_ID, Subject_Name, Subject_Code, amountRupees, amountEthers) VALUES ($student_id, '$subject_name', '$subject_code', '$amount_rupees', '$amount_ethers')";
                mysqli_query($conn, $insert_failed_subject_query);

                // Close database connection
                mysqli_close($conn);

                // Display success message
                echo "<script>alert('Student Inserted Successfully')</script>";

            } else {
                // Handle the case when form fields are not properly submitted
                echo "Error: Form fields are not properly submitted."; 
            }
        }
        ?>
        <div id="insert_error_message" style="display:none;"></div>
    </div>

    <div class="container">
        <div id="student_info">
            <h2>Student Details</h2>
           
            <?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_registration_number'])) {
    // Retrieve the registration number entered by the user
    $registration_number = $_POST['search_registration_number'];

    // Validate registration number format if needed

    // Establish database connection
    $conn = mysqli_connect("localhost", "root", "", "students");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve student details based on registration number
    $student_query = "SELECT * FROM Students WHERE Registration_Number = '$registration_number'";
    $student_result = mysqli_query($conn, $student_query);

    if (mysqli_num_rows($student_result) > 0) {
        // Display student details
        $student_row = mysqli_fetch_assoc($student_result); 
        echo "<p><strong>Registration Number:</strong> " . htmlspecialchars($student_row['Registration_Number'] ? $student_row['Registration_Number'] : "NULL") . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($student_row['Name'] ? $student_row['Name'] : "NULL") . "</p>";
        echo "<p><strong>Course:</strong> " . htmlspecialchars($student_row['course'] ? $student_row['course'] : "NULL") . "</p>";
        echo "<p><strong>Branch:</strong> " . htmlspecialchars($student_row['branch'] ? $student_row['branch'] : "NULL") . "</p>";

        // Retrieve failed subjects of the student including amount from transactions
        $failed_subjects_query = "SELECT failed_subjects.Failed_Subject_ID, failed_subjects.Subject_Name, failed_subjects.Subject_Code, transactions.Payment_Type, transactions.Transaction_ID, failed_subjects.amountEthers
                FROM Failed_Subjects
                LEFT JOIN transactions ON failed_subjects.Student_ID = transactions.Student_ID 
                WHERE Failed_Subjects.Student_ID = (SELECT Student_ID FROM Students WHERE Registration_Number = '$registration_number')";
$failed_subjects_result = mysqli_query($conn, $failed_subjects_query);


        if (mysqli_num_rows($failed_subjects_result) > 0) {
            echo "<h3>Failed Subjects:</h3>";
            echo "<form action='' method='post'>"; // Opening form tag
            echo "<table>";
            echo "<tr><th>Subject Name</th><th>Subject Code</th><th>Payment Type</th><th>Transaction ID</th><th>Amount</th><th>Update</th></tr>";
            while ($failed_subject_row = mysqli_fetch_assoc($failed_subjects_result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($failed_subject_row['Subject_Name'] ? $failed_subject_row['Subject_Name'] : "NULL") . "</td>";
                echo "<td>" . htmlspecialchars($failed_subject_row['Subject_Code'] ? $failed_subject_row['Subject_Code'] : "NULL") . "</td>";
                echo "<td>" . htmlspecialchars($failed_subject_row['Payment_Type'] ? $failed_subject_row['Payment_Type'] : "NULL") . "</td>";

                $transactionId = htmlspecialchars($failed_subject_row['Transaction_ID'] ? $failed_subject_row['Transaction_ID'] : "NULL");
                echo "<td><span class='copy-text' id='copy-text'><a href='#' onclick='redirectToEtherscan(\"$transactionId\")'>$transactionId</a></span></td>";

                echo "<td>" . htmlspecialchars($failed_subject_row['amountEthers'] ? $failed_subject_row['amountEthers'] : "NULL") . "</td>";
                echo "<td><input type='checkbox' name='select_subject[]' class='cyberpunk-checkbox' value='" . htmlspecialchars($failed_subject_row['Failed_Subject_ID']) . "' onchange='updateSelectedSubjects(this)'></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<input type='hidden' name='selected_subjects_array' id='selected_subjects_array'>";
            echo "</form>"; // Closing form tag

            // Display 'rupees' transactions if any
            $rupees_query = "SELECT * FROM transactions WHERE Student_ID = (SELECT Student_ID FROM Students WHERE Registration_Number = '$registration_number') AND Payment_Type = 'rupees'";
            $rupees_result = mysqli_query($conn, $rupees_query);

            if (mysqli_num_rows($rupees_result) > 0) {
                echo "<h3>Rupees Transactions:</h3>";
                echo "<table>";
                echo "<tr><th>Transaction ID</th><th>Amount in Rupees</th></tr>";
                while ($rupees_row = mysqli_fetch_assoc($rupees_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($rupees_row['Transaction_ID'] ? $rupees_row['Transaction_ID'] : "NULL") . "</td>";
                    echo "<td>" . htmlspecialchars($rupees_row['txn_amount'] ? $rupees_row['txn_amount'] : "NULL") . "</td>"; // Adjusted to fetch txn_amount
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No rupees transactions found.</p>";
            }
        } else {
            echo "<p>No failed subjects found.</p>";
        }

    } else {
        echo "<p>No Student found with the Registration Number : " . htmlspecialchars($registration_number) . "</p>";
    }

    // Close database connection
    mysqli_close($conn);
}
?>


        </div>
    </div>

    <?php
    // Check if the request method is POST and if the action is to delete subjects
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] === "delete_subjects") {
        // Retrieve the selected subjects array from the request
        if (isset($_POST["selected_subjects"])) {
            $selectedSubjectsArray = json_decode($_POST["selected_subjects"], true);

            // Establish connection to the database
            $conn = mysqli_connect("localhost", "root", "", "students");

            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Loop through the selected subjects array and delete each subject
            foreach ($selectedSubjectsArray as $subjectID) {
                // Perform SQL query to delete subject by ID
                $delete_query = "DELETE FROM Failed_Subjects WHERE Failed_Subject_ID = $subjectID";

                if (mysqli_query($conn, $delete_query)) {
                    // Subject deleted successfully
                    // You can log this or perform any additional actions
                } else {
                    // Error occurred while deleting subject
                    // Log the error or handle it as needed
                    error_log("Error deleting subject with ID $subjectID: " . mysqli_error($conn));
                }
            }

            // Close database connection
            mysqli_close($conn);

            // Redirect to the same page after deleting subjects
            header("Location: {$_SERVER['PHP_SELF']}");
            exit; // Terminate PHP execution after handling the request
        } else {
            // Handle missing or invalid data in the request
            echo "Invalid data received.";
            exit; // Terminate PHP execution after handling the request
        }
    }
    ?>
    <script src="adminPage.js"></script>
</body>

</html>