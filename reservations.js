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
function handlePhoneDetails(){
    window.location.href = "reservationsFormPhone.html";
}

    document.getElementById('deetsPhoneButton').onclick = handlePhoneDetails;

function handleAccDetails(){
    window.location.href = "reservationFormAcc.html";
}

    document.getElementById('deetsAccButton').onclick = handleAccDetails;

    
