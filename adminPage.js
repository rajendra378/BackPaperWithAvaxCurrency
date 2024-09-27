// JavaScript function to update selected subjects array
function updateSelectedSubjects(checkbox) {
  var selectedSubjectsArray = document
    .getElementById("selected_subjects_array")
    .value.split(",");
  var subjectID = checkbox.value;
  if (checkbox.checked && subjectID !== "") {
    // Check if subjectID is not empty
    selectedSubjectsArray.push(subjectID);
  } else {
    var index = selectedSubjectsArray.indexOf(subjectID);
    if (index !== -1) {
      selectedSubjectsArray.splice(index, 1);
    }
  }
  document.getElementById("selected_subjects_array").value =
    selectedSubjectsArray.join(",");
  console.log(selectedSubjectsArray);
}
// Client-side form validation
document
  .getElementById("insert_form")
  .addEventListener("submit", function (event) {
    var registrationNumber = document
      .getElementById("insert_registration_number")
      .value.trim();
    var name = document.getElementById("insert_name").value.trim();
    var branch = document.getElementById("insert_branch").value.trim();
    var course = document.getElementById("insert_course").value.trim();
    var subjectName = document
      .getElementById("insert_subject_name")
      .value.trim();
    var subjectCode = document
      .getElementById("insert_subject_code")
      .value.trim();
    var amountRupees = document
      .getElementById("insert_amount_rupees")
      .value.trim();
    var amountEthers = document
      .getElementById("insert_amount_ethers")
      .value.trim();

    if (
      !registrationNumber ||
      !name ||
      !branch ||
      !course ||
      !subjectName ||
      !subjectCode ||
      !amountRupees ||
      !amountEthers
    ) {
      // Display error message
      document.getElementById("insert_error_message").innerText =
        "All fields are required.";
      document.getElementById("insert_error_message").style.display = "block";
      // Prevent form submission
      event.preventDefault();
    }
  });

// Function to show the add form

function showAddForm() {
  var addForm = document.getElementById("addForm");
  addForm.style.display = "block";
  var student_info = document.getElementById("student_info");
  student_info.style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
  var copyTexts = document.querySelectorAll(".copy-text");
  copyTexts.forEach(function (copyText) {
    copyText.addEventListener("click", function (event) {
      var textToCopy = this.innerText || this.textContent;
      var tempInput = document.createElement("input");
      tempInput.value = textToCopy;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);

      // Remove any existing tooltip
      var existingTooltip = document.querySelector(".copied-tooltip");
      if (existingTooltip) {
        existingTooltip.parentNode.removeChild(existingTooltip);
      }

      // Create and position the tooltip
      var tooltip = document.createElement("span");
      tooltip.classList.add("copied-tooltip");
      tooltip.innerText = "Copied";
      tooltip.style.position = "absolute";
      tooltip.style.top = event.clientY - 20 + "px"; // 20px above the pointer
      tooltip.style.left = event.clientX + 10 + "px"; // 10px to the right of the pointer
      tooltip.style.backgroundColor = "#319";
      tooltip.style.color = "#fff";
      tooltip.style.padding = "5px";
      tooltip.style.borderRadius = "5px";
      tooltip.style.zIndex = "9999"; // Ensure tooltip appears on top of other elements
      document.body.appendChild(tooltip);

      // Remove the tooltip after 1 second
      setTimeout(function () {
        document.body.removeChild(tooltip);
      }, 500);
    });
  });
});



function redirectToEtherscan(transactionId) {
    if (transactionId && transactionId !== 'NULL') {
        var etherscanLink = "https://sepolia.etherscan.io/tx/" + transactionId;
        window.open(etherscanLink, '_blank');
    } else {
        alert("Invalid Transaction ID");
    }
}





function deleteSelectedSubjects() {
  console.log("Delete button clicked!"); // Debugging message
  var selectedSubjectsArray = document
    .getElementById("selected_subjects_array")
    .value.split(",");
  console.log("Selected Subjects Array:", selectedSubjectsArray); // Debugging message

  // Check if any subject is selected
  if (selectedSubjectsArray.filter(Boolean).length === 0) {
    alert("Please select subjects to delete.");
    return;
  }

  // Set the value of the hidden input field
  document.getElementById("selectedSubjectsInput").value = JSON.stringify(
    selectedSubjectsArray
  );

  // Submit the form
  document.getElementById("deleteSubjectsForm").submit();
}
