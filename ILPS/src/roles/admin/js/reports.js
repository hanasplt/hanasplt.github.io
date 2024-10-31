// Pagination variables
let currentPage = 1;
const rowsPerPage = 6;

// Function to populate the table with data
function populateTable(filteredData) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = ''; // Clear current table contents

    // Calculate start and end index for current page
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const paginatedData = filteredData.slice(startIndex, endIndex);

    paginatedData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.eventName}</td>
            <td>${item.teamName}</td>
            <td>${item.total_score}</td>
            <td>${item.action_made}</td>
            <td>${item.action_at}</td>
        `;
        tableBody.appendChild(row);
    });

    // Update pagination buttons
    updatePagination(filteredData.length);
}

// Function to update pagination buttons
function updatePagination(totalItems) {
    const totalPages = Math.ceil(totalItems / rowsPerPage);
    const paginationDiv = document.getElementById('pagination');
    
    if (!paginationDiv) {
        return;
    }

    paginationDiv.innerHTML = `
        <button class="prevbtn ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage('prev')" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
        <span>Page ${currentPage} of ${totalPages}</span>
        <button class="nextbtn ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage('next')" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
    `;
}

// Function to handle page changes
function changePage(action) {
    const totalPages = Math.ceil(data.length / rowsPerPage);

    if (action === 'prev' && currentPage > 1) {
        currentPage--;
    } else if (action === 'next' && currentPage < totalPages) {
        currentPage++;
    }

    filterTable();
}

// Populate and Filter Table
function filterTable() {
    const filter = document.getElementById('filterOpt').value;
    const ev_filter = document.getElementById('eventFilter').value;
    let filteredData;

    // Display all today
    if (ev_filter === 'all' && filter === 'today') {
        const today = new Date().toISOString().split("T")[0];
        // Filter data to include only items where the action_at date is today
        filteredData = data.filter(item => {
            const dbDateToday = new Date(item.action_at).toISOString().split("T")[0];
            return dbDateToday === today;
        });
    }
    // Display all this year
    else if (ev_filter === 'all' && filter === 'all') {
        const currYear = new Date().getFullYear();
        filteredData = data.filter(item => new Date(item.action_at).getFullYear() === currYear);
    } 
    // Display all in selected year
    else if (ev_filter === 'all') {
        const selectedYear = parseInt(filter);
        filteredData = data.filter(item => new Date(item.action_at).getFullYear() === selectedYear);
    }
    
    // Display specific event today
    if (ev_filter !== 'all' && filter === 'today') {
        const today = new Date().toISOString().split("T")[0];
        filteredData = data.filter(item => {
            const dbDateToday = new Date(item.action_at).toISOString().split("T")[0];
            return dbDateToday === today && item.eventName === ev_filter;
        });
    }
    // Display specific this year
    else if (ev_filter !== 'all' && filter === 'all') {
        const currYear = new Date().getFullYear();
        filteredData = data.filter(item => 
            new Date(item.action_at).getFullYear() === currYear &&
            item.eventName === ev_filter
        );
    } 
    // Display specific in selected year
    else if (ev_filter !== 'all') {
        const selectedYear = parseInt(filter);
        filteredData = data.filter(item => 
            new Date(item.action_at).getFullYear() === selectedYear &&
            item.eventName === ev_filter
        );
    }

    const tableBody = document.getElementById('tableBody');
    if (filteredData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5">No data available for the selected year.</td></tr>';
        if (document.getElementById('pagination')) {
            document.getElementById('pagination').innerHTML = '';
        }
        return;
    }

    populateTable(filteredData);
}

// Initial load with 'Today' and 'All' filter selected
if (document.getElementById('filterOpt') && document.getElementById('eventFilter')) {
    document.getElementById('filterOpt').value = 'today';
    document.getElementById('eventFilter').value = 'all';
    filterTable(); 
}


// Redirect to overall scoresheets per event
var sheet = document.getElementById('score_sheets');
if (sheet) {
    document.getElementById('score_sheets').addEventListener('click', function() {
        window.location.href = "./reportScoreSheet.php";
    });

}
// Redirect to main report page
var mainpage = document.getElementById('backToScore');
if (mainpage) {
    document.getElementById('backToScore').addEventListener('click', function() {
        window.location.href = "./reports.php";
    });
}


// Validate form submission for export
function submitForm(actionUrl) {
    // Set the form action to the specified file
    document.getElementById('exportForm').action = actionUrl;

    // Submit the form
    document.getElementById('exportForm').submit();
}


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