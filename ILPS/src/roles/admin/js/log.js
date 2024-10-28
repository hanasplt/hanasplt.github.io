let currentPage = 1;
let totalPages = 1;

// Display Access Log
window.onload = function() {
    // Initial load
    loadAccessLog(currentPage);
    populateYearFilter();
};

// Loads the log based on the year selected
function loadAccessLog(page = 1, year = "") {
    var table = document.getElementById("tableLog"); // Get the table id

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                const response = JSON.parse(this.responseText);

                // Assign totalPages from the response
                totalPages = response.totalPages;
                
                // Start ID number for display
                const startId = (page - 1) * 10 + 1; // Assuming 10 logs per page
                const logsHTML = response.logs.map((log, index) => 
                    `<tr>
                        <td>${startId + index}</td> <!-- Display sequential ID -->
                        <td>${log.date_on}</td>
                        <td>${log.fullname}</td>
                        <td>${log.actions}</td>
                    </tr>`).join('');

                document.querySelector("#" + table.id + " tbody").innerHTML = logsHTML;

                // Check if there are any logs to display
                if (response.logs.length > 0) {
                    setupPagination(totalPages, page);
                    document.getElementById('paginationControls').style.display = ''; // Show pagination
                } else {
                    // Reset currentPage to 1 if no logs are found
                    currentPage = 1; 
                    totalPages = 1; 

                    document.querySelector("#" + table.id + " tbody").innerHTML = "<tr><td colspan='4'>No logs found for the selected year.</td></tr>";
                    setupPagination(totalPages, currentPage); 
                    document.getElementById('paginationControls').style.display = 'none'; // Hide pagination if no logs
                }
            } else {
                console.error("Error fetching logs: " + this.statusText);
            }
        }
    };

    xhttp.open("POST", "get_logs.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("page=" + page + "&year=" + year); // Send the year filter as well
}


// Setup pagination controls
function setupPagination(totalPages, currentPage) {
    // Always show the page number info, even if there's only one page
    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;

    if (totalPages > 1) {
        document.getElementById('paginationControls').style.display = ''; // Show pagination buttons
        document.getElementById('prevBtn').disabled = currentPage === 1; // Disable 'Previous' if on the first page
        document.getElementById('nextBtn').disabled = currentPage === totalPages; // Disable 'Next' if on the last page
    } else {
        // Hide the 'Previous' and 'Next' buttons, but keep the page number visible
        document.getElementById('paginationControls').style.display = 'none';
    }
}


// Change page function
function changePage(newPage) {
    if (newPage > 0 && newPage <= totalPages) { // Check the new page range
        currentPage = newPage; // Update the current page
        loadAccessLog(currentPage); // Load logs for the new page
    }
}


// Function to populate the year filter dropdown
function populateYearFilter() {
    const yearFilter = document.getElementById('yearFilter');
    const currentYear = new Date().getFullYear(); // Get the current year
    
    // Loop from 2000 to current year and create option elements
    for (let i = 2022; i <= currentYear; i++) {
        yearFilter.innerHTML += `<option value="${i}">${i}</option>`;
    }
}

// Function to filter table rows based on selected year
document.getElementById('yearFilter').addEventListener('change', function() {
    let selectedYear = this.value;
    currentPage = 1; // Reset to page 1 when filtering by year
    loadAccessLog(currentPage, selectedYear); // Load logs based on selected year
});


// Validate form submission
function submitForm(actionUrl) {
    const yearFilter = document.getElementById('yearFilter').value;

    if (!yearFilter) { // If no year is selected
        // Prevent form submission and show warning
        Swal.fire({
            title: 'Oops!',
            text: 'Please select a year before exporting.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else {
        // Set the form action to the specified file
        document.getElementById('exportForm').action = actionUrl;

        // Submit the form
        document.getElementById('exportForm').submit();
    }
}


// LOGOUT CONFIRMATION
document.getElementById('logout').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7FD278',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log me out',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the login page
            window.location.href = 'get_logs.php?logout';
        }
    });
});
