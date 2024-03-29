/* log out*/
function handleLogout(){
    window.location.href = "index.html";
}

    document.getElementById('logoutButton').onclick = handleLogout;

/*dashboard*/
function handleDashboard(){
    window.location.href = "dashboard.html";
}

    document.getElementById('dashButton').onclick = handleDashboard;

/*appointments*/
function handleAppointments(){
    window.location.href = "appointments.html";
}

    document.getElementById('appButton').onclick = handleAppointments;

/*reservations*/
function handleReservations(){
    window.location.href = "reservations.html";
}

    document.getElementById('reservButton').onclick = handleReservations;

    
/*date*/
var currentDate = new Date();
var options = {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
};
var formattedDate = currentDate.toLocaleDateString('en-US', options);
document.getElementById('date').innerHTML = formattedDate;


/*menu*/
document.addEventListener("DOMContentLoaded", function() {
    const dash = document.getElementById('dash');
    const menu = document.querySelector('.menu');
    const icon = document.getElementById('menuIcon');

    menu.style.display = 'none';
    
    function toggleMenu() {
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            menu.style.display = 'none';
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }
    
    dash.addEventListener('click', toggleMenu);
    
    icon.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleMenu();
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const searchButton = document.getElementById('searchButton');

    searchButton.addEventListener('click', function() {
        const searchTerm = document.getElementById('searchInput').value;

        if (searchTerm.trim() !== '') {
            alert('Searching for: ' + searchTerm);
        } else {
            alert('Please enter a search term.');
        }
    });
});

/*details*/
function handleDetails(){
    window.location.href = "appointmentsForm.html";
}

    document.getElementById('deetsButton').onclick = handleDetails;


// Get the container and its children
const container = document.getElementById("reserv-data");
const items = Array.from(container.children);

// Function to sort items based on selected option
function sortItems(option) {
    items.sort((a, b) => {
        const categoryA = a.getAttribute("data-category");
        const categoryB = b.getAttribute("data-category");
        if (option === 'all') return 0; // No sorting needed
        if (option === 'phone') return categoryA === 'phone' ? -1 : 1;
        if (option === 'accessories') return categoryA === 'accessories' ? -1 : 1;
        return 0;
    });

    // Clear the container
    container.innerHTML = '';

    // Append the sorted items back to the container
    items.forEach(item => {
        container.appendChild(item);
    });
}

// Event listener for select element
document.getElementById('choice-sort').addEventListener('change', function() {
    sortItems(this.value);
});

