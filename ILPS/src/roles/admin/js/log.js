// Display Access Log
window.onload = function() {
    loadAccessLog();
};

function loadAccessLog() {
    // Fetch the logs
    var table = document.getElementById("tableLog"); // Get the table id

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + table.id + " tbody").innerHTML = this.responseText;
        }
    };
    
    xhttp.open("POST", "../get_logs.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

// Function to filter table rows based on selected year
document.getElementById('yearFilter').addEventListener('change', function() {
    let selectedYear = this.value;
    let table = document.getElementById('tableLog');
    let rows = table.getElementsByTagName('tr');
    let msg = document.getElementById('db-msg');
    
    // Loop through rows and display only those matching the selected year
    for (let i = 1; i < rows.length; i++) { // Start at 1 to skip the table header
        let dateCell = rows[i].getElementsByTagName('td')[1];
        let rowDate = new Date(dateCell.textContent);
        let rowYear = rowDate.getFullYear();

        // Show or hide the row based on the selected year
        if (selectedYear === "" || rowYear == selectedYear) {
            rows[i].style.display = '';
            msg.innerText = '';
        } else {
            rows[i].style.display = 'none';
            msg.innerText = 'No Activities Exists.';
        }
    }
});

//LOGOUT CONFIRMATION
document.getElementById('logoutIcon').addEventListener('click', function() {
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
            // mag redirect siya to the login page
            window.location.href = '../get_logs.php?logout';
        }
    });
});