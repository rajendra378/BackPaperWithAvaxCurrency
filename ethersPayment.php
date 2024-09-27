<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="ethersPayment.css">
    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.0/dist/ethers.umd.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="card">
            <?php
            session_start(); // Start the session
            
            // Check if the registration number is provided in the URL
            if (isset($_GET['registration_number'])) {
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
                        <th>Ethers</th>
                    </tr>
                </thead>
                <tbody id="selectedRowsBody"></tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h2>Pay with Crypto</h2>
        <!-- Placeholder content for cryptocurrency payment -->
        <div class="cryptoPaymentContent">
            <h1>Transfer Funds via MetaMask</h1>
            <div id="accountDetailsDiv">
                <!-- Account details will be displayed here -->
            </div>
            <br />
            <button id="connectButton">Connect Metamask</button>
            <!-- Add id attribute to the Transfer Funds button -->
            <div id="amount"></div>
            <button id="transferButton">Transfer Funds</button>
        </div>
    </div>
    <div id="transactionDetailsDiv" class="receipt" style="display: none;">
        <!-- Transaction details will be displayed here -->
        <h1>Receipt</h1>
        <?php
        // Check if student details are available
        if (isset($registrationNumber) && isset($name) && isset($branch) && isset($course)) {
            echo "<p><strong>Registration Number:</strong> $registrationNumber</p>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Branch:</strong> $branch</p>";
            echo "<p><strong>Course:</strong> $course</p>";
        }
        ?>
        <p><strong>Payment Type:</strong> Ethers</p>
        <p><strong>Amount:</strong> <span id="paymentAmount"></span> Eth</p>
        <p><strong>Transaction ID:</strong> <span id="transactionId"></span></p>
        <p><strong>From Address:</strong> <span id="fromAddress"></span></p>
        <p><strong>To Address:</strong> <span id="toAddress"></span></p>
        <p><strong>Timestamp:</strong> <span id="timestamp"></span></p>
        <h2>Selected Subjects</h2>
        <table>
            <thead>
                <tr>
                    <th>Index</th>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Ethers</th>
                </tr>
            </thead>
            <tbody id="additionalTableBody"></tbody>
        </table>
        <!-- Add print button here -->
        <button id="printButton">Print Receipt</button>
    </div>

    <script src="ethersPayment.js"></script>
</body>

</html>