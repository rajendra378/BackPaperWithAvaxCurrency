
        document.getElementById("amount").innerText = sessionStorage.getItem('rupeesSum');
        var selectedRows = JSON.parse(sessionStorage.getItem("selectedRows"));

        var selectedRowsBody = document.getElementById("selectedRowsBody");
        var index = 1;
        if (selectedRows) {
            selectedRows.forEach(function (row) {
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
                <td>${index}</td>
                <td>${row.Subject_Name}</td>
                <td>${row.Subject_Code}</td>
                <td>${row.Rupees}</td>
            `;
                selectedRowsBody.appendChild(newRow);
                index++;
            });
        }

        var additionalTableBody = document.getElementById("additionalTableBody");
        if (selectedRows) {
            selectedRows.forEach(function (row, index) {
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
                <td>${index + 1}</td>
                <td>${row.Subject_Name}</td>
                <td>${row.Subject_Code}</td>
                <td>${row.Rupees}</td>
            `;
                additionalTableBody.appendChild(newRow);
            });
        }

        const payButton = document.getElementById("payButton");

        payButton.addEventListener("click", function () {
            const registrationNumber = "<?php echo isset($registrationNumber) ? $registrationNumber : 'null'; ?>";
            const rupeesSum = sessionStorage.getItem("rupeesSum");
            const selectedRows = JSON.parse(sessionStorage.getItem("selectedRows"));

            var options = {
                "key": "rzp_test_byPLZtNtsJuOI3",
                "amount": rupeesSum * 100, // Amount is in currency subunits. Default currency is INR.
                "currency": "INR",
                "name": "Centurion University of Technology and Mangement",
                "description": "Test Transaction",
                "handler": function (response) {
                    sessionStorage.setItem("transactionId", response.razorpay_payment_id);
                    document.getElementById("printButton").style.display = "block";
                    displayReceipt();

                    fetch('update_database.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            transactionId: sessionStorage.getItem("transactionId"),
                            registrationNumber: registrationNumber,
                            rupeesSum: rupeesSum,
                            selectedRows: selectedRows
                        })
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            alert("Payment successful and database updated!");
                            document.getElementById("payButton").style.display = "none";
                        } else {
                            alert("Payment successful but database update failed!");
                        }
                    }).catch(error => {
                        console.error("Error updating database:", error);
                    });
                },
                "prefill": {
                    "name": "<?php echo isset($name) ? $name : ''; ?>",
                    "email": "user@example.com",
                    "contact": "Enter Your Mobile Number"
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
        });

        function displayReceipt() {
            document.getElementById("transactionDetailsDiv").style.display = "block";
            document.getElementById("paymentAmount").innerText = sessionStorage.getItem("rupeesSum");
            document.getElementById("transactionId").innerText = sessionStorage.getItem("transactionId");
        }

      
    // Function to handle printing the receipt
    function printReceipt() {
        // Hide the print button to prevent multiple prints
        document.getElementById("printButton").style.display = "none";
        // Print only the receipt section
        window.print();
        // Show the print button again after printing is done
        document.getElementById("printButton").style.display = "block";
    }

    // Event listener for the print button
    document.getElementById("printButton").addEventListener("click", function () {
        printReceipt();
    });



