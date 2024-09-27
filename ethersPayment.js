const registrationNumber =
  "<?php echo isset($registrationNumber) ? $registrationNumber : 'null'; ?>";

// Retrieve selected rows from session storage
document.getElementById("amount").innerText =
  sessionStorage.getItem("ethersSum");
var selectedRows = JSON.parse(sessionStorage.getItem("selectedRows"));

// Display selected rows
var selectedRowsBody = document.getElementById("selectedRowsBody");
var index = 1;
if (selectedRows) {
  selectedRows.forEach(function (row) {
    var newRow = document.createElement("tr");
    newRow.innerHTML = `
                <td>${index}</td>
                <td>${row.Subject_Name}</td>
                <td>${row.Subject_Code}</td>
                <td>${row.Ethers}</td>
            `;
    selectedRowsBody.appendChild(newRow);
    index++;
  });
}

// Display selected rows in the receipt
var additionalTableBody = document.getElementById("additionalTableBody");
if (selectedRows) {
  selectedRows.forEach(function (row, index) {
    var newRow = document.createElement("tr");
    newRow.innerHTML = `
                <td>${index + 1}</td>
                <td>${row.Subject_Name}</td>
                <td>${row.Subject_Code}</td>
                <td>${row.Ethers}</td>
            `;
    additionalTableBody.appendChild(newRow);
  });
}

const connectButton = document.getElementById("connectButton");
const accountDetailsDiv = document.getElementById("accountDetailsDiv");

async function updateAccountDetails(accounts) {
  try {
    const selectedAddress = ethereum.selectedAddress;
    const provider = new ethers.providers.Web3Provider(window.ethereum);
    const balance = await provider.getBalance(selectedAddress);
    if (!selectedAddress) {
      alert(
        "No account selected in MetaMask. Please select an account to proceed."
      );
      return;
    }

    // Convert balance from Wei to Ether
    // const balanceInEther = ethers.utils.formatEther(balance);

    // Update account details in HTML
    accountDetailsDiv.innerHTML = `
                <p>Connected account: ${selectedAddress}</p>
            `;
  } catch (error) {
    console.error("Error updating account details:", error);
  }
}

async function transferFunds() {
  if (!ethereum.selectedAddress) {
    alert("Please connect to the wallet");
    return;
  }
  const walletAddress = "0x6Db1056064db0cF9C03A6e955111E999bbdb5959";

  const ethersSum = sessionStorage.getItem("ethersSum");
  const selectedRows = JSON.parse(sessionStorage.getItem("selectedRows")); // Retrieve selected rows data

  try {
    const provider = new ethers.providers.Web3Provider(window.ethereum);
    const signer = provider.getSigner();
    const selectedAddress = signer.getAddress();
    if (!selectedAddress) {
      throw new Error(
        "No account selected in MetaMask. Please select an account to proceed."
      );
    }

    // Update the database with transaction details and selected rows data

    const transactionResponse = await signer.sendTransaction({
      to: walletAddress,
      value: ethers.utils.parseEther(ethersSum.toString()),
    });
    console.log("walletAddress");

    // Store transaction details in session storage
    sessionStorage.setItem("transactionId", transactionResponse.hash);
    sessionStorage.setItem("fromAddress", selectedAddress);
    sessionStorage.setItem("toAddress", walletAddress);
    console.log("walletAddress");
    sessionStorage.setItem("timestamp", new Date().toLocaleString());
    document.getElementById("printButton").style.display = "block";

    // Display receipt
    displayReceipt();

    const response = await fetch("update_database.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        transactionId: sessionStorage.getItem("transactionId"),
        registrationNumber: registrationNumber,
        ethersSum: ethersSum,
        selectedRows: selectedRows, // Include selected rows data
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to update database");
    }
    document.querySelector(".cryptoPaymentContent").style.display = "none";
    // Show print button
    document.getElementById("printButton").style.display = "block";

    alert("Transaction sent successfully!");
  } catch (error) {
    console.error("Error:", error.message);
    alert(
      "Failed to send transaction. Please check console for error details."
    );
  }
}

function displayReceipt() {
  // Retrieve payment details from session storage
  var ethersSum = sessionStorage.getItem("ethersSum");
  var transactionId = sessionStorage.getItem("transactionId");
  var fromAddress = sessionStorage.getItem("fromAddress");
  var toAddress = sessionStorage.getItem("toAddress");
  var timestamp = sessionStorage.getItem("timestamp");

  // Display payment details in the receipt
  document.getElementById("paymentAmount").textContent = ethersSum;
  document.getElementById("transactionId").textContent = transactionId;
  document.getElementById("fromAddress").textContent = fromAddress;
  document.getElementById("toAddress").textContent = toAddress;
  document.getElementById("timestamp").textContent = timestamp;

  // Display receipt
  document.getElementById("transactionDetailsDiv").style.display = "block";

  // Show print button
  document.getElementById("printButton").style.display = "block";
}

// Function to print receipt
function printReceipt() {
  var printContents = document.getElementById(
    "transactionDetailsDiv"
  ).innerHTML;
  var originalContents = document.body.innerHTML;
  var printWindow = window.open("", "_blank");
  printWindow.document.body.innerHTML = printContents;
  printWindow.print();
}

// Bind event listener for the print button
document.getElementById("printButton").addEventListener("click", printReceipt);

// Bind event listener for the "Transfer Funds" button
document
  .getElementById("transferButton")
  .addEventListener("click", transferFunds);

// Bind event listener for connecting MetaMask
connectButton.addEventListener("click", async () => {
  if (typeof window.ethereum !== "undefined") {
    try {
      const accounts = await window.ethereum.request({
        method: "eth_requestAccounts",
      });
      await updateAccountDetails(accounts);
      window.ethereum.on("accountsChanged", updateAccountDetails);
    } catch (error) {
      console.error("Error connecting to MetaMask:", error);
    }
  } else {
    console.error("Metamask not detected.");
  }
});

// Handling MetaMask availability and network change
window.ethereum.on("chainChanged", function (chainId) {
  alert("Please switch to the correct network to proceed.");
});
