let currentPage = 1;
let totalPages = 1;


// Format the date and time as word
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        month: 'long', 
        day: 'numeric', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit', 
        hour12: true };
    return date.toLocaleString('en-US', options);
}

// Display Access Log
window.onload = function() {
    // Initial load
    loadAccessLog(currentPage);
};

// Loads the log based on the year selected
function loadAccessLog(page = 1) {
    var table = document.getElementById("tableLog"); // Get the table id

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                const response = JSON.parse(this.responseText);

                // Assign totalPages from the response
                totalPages = response.totalPages;
                
                // Generate the logs table rows based on the response
                const logsHTML = response.logs.map((log) => 
                    `<tr>
                        <td>${log.logId}</td> <!-- Use logId from the response -->
                        <td>${formatDate(log.date_on)}</td>
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
    xhttp.send("page=" + page); // Send the year filter as well
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


// Validate form submission
function submitForm(actionUrl) {
    // Set the form action to the specified file
    document.getElementById('exportForm').action = actionUrl;

    // Submit the form
    document.getElementById('exportForm').submit();
}


/* BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */
function showConfirmationMsg() {
    // Backup and Drop database confirmation
    Swal.fire({
        title: 'Database Reset Confirmation',
        html: "<p style='text-align: left;'><b>Warning</b>: You are about to perform a database backup and reset. This will:</br>1. <b>Download a backup</b> of your current data</br>2. <b>Permanently delete</b> all records from the database</br></br>This action <b><u>cannot be undone</u></b>.</p>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7FD278',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Backup and Reset',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../backup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=backup'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Download the data
                    downloadBackup(data.file);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    })
                }
            })
            .catch(error => {
                console.log('Exception Error: '+error.message);
            })
        }
    });
}

function downloadBackup(file) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../backup.php';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'download';

    const fileInput = document.createElement('input');
    fileInput.type = 'hidden';
    fileInput.name = 'file';
    fileInput.value = file;

    form.appendChild(actionInput);
    form.appendChild(fileInput);
    document.body.appendChild(form);
    form.submit();

    setTimeout(() => {
        Swal.fire({
            title: 'Redirecting...',
            text: 'Your backup has been downloaded successfully!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        }).then(() => {
            window.location.href = '../../index.html';
        });
    }, 1000);
    
    document.body.removeChild(form);
}
/* END BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */


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
