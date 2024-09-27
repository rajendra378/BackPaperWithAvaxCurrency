
var checkboxes = document.querySelectorAll('input[type="checkbox"]');
var rupeesPaymentBtn = document.getElementById('rupeesPayment');
var ethersPaymentBtn = document.getElementById('ethersPayment');

checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener("change", function () {
        // If this checkbox is checked, uncheck the other checkbox in the row
        if (this.checked) {
            checkboxes.forEach(function (cb) {
                if (cb !== checkbox && cb.parentNode.parentNode === checkbox.parentNode.parentNode) {
                    cb.checked = false;
                }
            });
        }
        updateSums();
        storeSelectedRows(); // Call the function to store selected rows
    });
});

function updateSums() {
    var rupeesSum = 0;
    var ethersSum = 0;
    var tableBody = document.getElementById("tableBody");

    const rows = tableBody.querySelectorAll("tr");

    rows.forEach(function (row) {
        const checkboxes = row.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                // Check if checkbox is checked
                const name = checkbox.name; // Get the value attribute of the checkbox

                if (name === "rupeesCheckBox[]") {
                    rupeesSum += parseFloat(checkbox.value);
                }
                if (name === "ethersCheckBox[]") {
                    ethersSum += parseFloat(checkbox.value);
                }
            }
        });
    });

    // Update paragraph elements with calculated sums
    const rupeesElement = document.getElementById("rupe");
    const ethersElement = document.getElementById("ethe");
    rupeesElement.innerHTML = rupeesSum;
    ethersElement.innerHTML = ethersSum;

    // Enable/disable payment buttons based on sums
    if (rupeesSum > 0) {
        rupeesPaymentBtn.classList.remove('disabled');
    } else {
        rupeesPaymentBtn.classList.add('disabled');
    }

    if (ethersSum > 0) {
        ethersPaymentBtn.classList.remove('disabled');
    } else {
        ethersPaymentBtn.classList.add('disabled');
    }
    
    // Update session storage with sums
    sessionStorage.setItem("ethersSum", ethersSum);
    sessionStorage.setItem("rupeesSum", rupeesSum);
}

function storeSelectedRows() {
    var selectedRows = [];
    var tableBody = document.getElementById("tableBody");
    const rows = tableBody.querySelectorAll("tr");

    rows.forEach(function (row) {
        const checkboxes = row.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                // If checkbox is checked, store the row data
                var rowData = {
                    "Subject_Name": row.cells[1].textContent,
                    "Subject_Code": row.cells[2].textContent,
                    "Rupees": row.cells[3].textContent,
                    "Ethers": row.cells[4].textContent
                };
                selectedRows.push(rowData);
            }
        });
    });

    // Store selected rows in session storage as JSON
    sessionStorage.setItem("selectedRows", JSON.stringify(selectedRows));
}

