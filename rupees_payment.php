<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rupees Payment</title>
    <link rel="stylesheet" href="./css/rupeesPayment.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link rel="stylesheet" href="rupees_payment.css">
    
    <style media="print">
    body * {
        display: none;
    }

    .receipt, .receipt * {
        display: block !important;
    }
</style>

</head>
<body>
    <div class="container">
        <div class="card">
            <?php
            session_start();if (isset($_GET['registration_number'])) {
                $registrationNumber = $_GET['registration_number'];

                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "students";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch student details from database
                $sql = "SELECT Registration_Number, Name, branch, course FROM students WHERE Registration_Number = '$registrationNumber'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output student details
                    while ($row = $result->fetch_assoc()) {
                        // Store student details in variables
                        $registrationNumber = $row['Registration_Number'];
                        $name = $row['Name'];
                        $branch = $row['branch'];
                        $course = $row['course'];

                        // Output student details
                        echo "<h2>Student Details</h2>";
                        echo "<p>Registration Number: " . $registrationNumber . "</p>";
                        echo "<p>Name: " . $name . "</p>";
                        echo "<p>Branch: " . $branch . "</p>";
                        echo "<p>Course: " . $course . "</p>";
                    }
                } else {
                    echo "<p>No student details found.</p>";
                }

                $conn->close();
            } else {
                echo "<p>No registration number provided.</p>";
            }
            ?>
        </div>
        <div class="card">
            <h2>Selected Subjects</h2>
            <table>
                <thead>
                    <tr>
                        <th>Index</th>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Rupees</th>
                    </tr>
                </thead>
                <tbody id="selectedRowsBody"></tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h2>Pay with Rupees</h2>
        <div class="rupeesPaymentContent">
            <h1>Complete Payment via Razorpay</h1>
            <div id="amount"></div>
            <button id="payButton">Pay Now</button>
        </div>
    </div>
    <div id="transactionDetailsDiv" class="receipt" style="display: none;">
        <h1>Receipt</h1>
        <?php
        if (isset($registrationNumber) && isset($name) && isset($branch) && isset($course)) {
            echo "<p><strong>Registration Number:</strong> $registrationNumber</p>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Branch:</strong> $branch</p>";
            echo "<p><strong>Course:</strong> $course</p>";
        }
        ?>
        <p><strong>Payment Type:</strong> Rupees</p>
        <p><strong>Amount:</strong> <span id="paymentAmount"></span> INR</p>
        <p><strong>Transaction ID:</strong> <span id="transactionId"></span></p>
        <h2>Selected Subjects</h2>
        <table>
            <thead>
                <tr>
                    <th>Index</th>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Rupees</th>
                </tr>
            </thead>
            <tbody id="additionalTableBody"></tbody>
        </table>
        <button id="printButton">Print Receipt</button>
    </div>

    <script src="rupees_payment.js"></script>
</body>

</html>