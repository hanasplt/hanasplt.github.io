/* log out*/
function handleLogout(){
    window.location.href = "adminLogin.html";
}

    document.getElementById('logoutButton').onclick = handleLogout;

/*dashboard*/
function handleDashboard(){
    window.location.href = "dashboard.html";
}

    document.getElementById('dashButton').onclick = handleDashboard;

/*appointments*/
function handleAppointments(){
    window.location.href = "appointments.php";
}

    document.getElementById('appButton').onclick = handleAppointments;
    document.getElementById('appts').onclick = handleAppointments;

/*reservations*/
function handleReservations(){
    window.location.href = "reservations.html";
}

    document.getElementById('reservButton').onclick = handleReservations;
    document.getElementById('resv').onclick = handleReservations;

/*reservations*/
function handleInventory(){
    window.location.href = "inventory-land.php";
}

    document.getElementById('invButton').onclick = handleInventory;
    document.getElementById('stcks').onclick = handleInventory;

/*history*/
function handleHistory(){
    window.location.href = "history.html";
}

    document.getElementById('historyButton').onclick = handleHistory;

/*billing*/
function handleBilling(){
    window.location.href = "billing.html";
}

    document.getElementById('billButton').onclick = handleBilling;    

    
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
    fetch('dashboard-data.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('content-count-appointments').innerHTML = data.appointments;
        document.getElementById('content-count-reservations').innerHTML = data.reservations;
        document.getElementById('content-count-inventory').innerHTML = data.inventory;
    })
    .catch(error => console.error('Error:', error));
});
