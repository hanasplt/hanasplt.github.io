// Function to populate the table with data
function populateTable(filteredData) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = ''; // Clear current table contents

    filteredData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${item.eventName}</td><td>${item.teamName}</td><td>${item.total_score}</td><td>${item.scored_at}</td><td>${item.updatedscore_at}</td>`;
        tableBody.appendChild(row);
    });
    
}

// Function to filter the table based on dropdown selection
function filterTable() {
    const filter = document.getElementById('filterOpt').value;
    let filteredData;

    if (filter === 'today') {
        const today = new Date().toISOString().split("T")[0];
        // Filter data to include only items where the scored_at date is today
        filteredData = data.filter(item => {
            const dbDateToday = new Date(item.scored_at).toISOString().split("T")[0];
            return dbDateToday === today;
        });
    }
    else if (filter === 'all') {
        const currYear = new Date().getFullYear();
        console.log(currYear);
        filteredData = data.filter(item => new Date(item.scored_at).getFullYear() === currYear);
    } else {
        const selectedYear = parseInt(filter);
        console.log(selectedYear);
        filteredData = data.filter(item => new Date(item.scored_at).getFullYear() === selectedYear);
    }

    
    if (filteredData.length === 0) {
        // Display no row data message
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '<tr><td colspan="5">No data available for the selected year.</td></tr>';
        return; // Exit the function if no data is found
    }

    populateTable(filteredData);
}

// Initial load with 'Today' filter selected
document.getElementById('filterOpt').value = 'today';
filterTable();

// Logout
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
        // mag redirect siya to the login page
        window.location.href = 'reports.php?logout';
      }
    });
  });