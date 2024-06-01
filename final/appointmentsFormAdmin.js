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
    window.location.href = "appointments.html";
}

    document.getElementById('appButton').onclick = handleAppointments;

/*reservations*/
function handleReservations(){
    window.location.href = "reservations.html";
}

    document.getElementById('reservButton').onclick = handleReservations;

/*reservations*/
function handleInventory(){
    window.location.href = "inventory.html";
}

    document.getElementById('invButton').onclick = handleInventory;

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

/*search*/
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

/*back*/
function handleBack(){
    window.location.href = "appointments.php";
}

    document.getElementById('backButton').onclick = handleBack;

/*confirm sweet alert*/
document.getElementById('confirmButton').addEventListener('click', function () {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to confirm this appointment?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, confirm it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const apptId = "<?php echo htmlspecialchars($appointment['appt_id']); ?>";
            const firstName = "<?php echo htmlspecialchars($appointment['appt_first_name']); ?>";
            const lastName = "<?php echo htmlspecialchars($appointment['appt_last_name']); ?>";
            const phoneNumber = "<?php echo htmlspecialchars($appointment['appt_phone_number']); ?>";
            const email = "<?php echo htmlspecialchars($appointment['appt_email']); ?>";
            const brandModel = "<?php echo htmlspecialchars($appointment['appt_brand_model']); ?>";
            const unitIssue = "<?php echo htmlspecialchars($appointment['appt_unit_issue']); ?>";

            fetch('confirm_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    appt_id: apptId,
                    appt_first_name: firstName,
                    appt_last_name: lastName,
                    appt_phone_number: phoneNumber,
                    appt_email: email,
                    appt_brand_model: brandModel,
                    appt_unit_issue: unitIssue,
                    services_rendered: "Services to be rendered", // Placeholder, update as needed
                    services_fee: 0.0, // Placeholder, update as needed
                    services_bill_date: new Date().toISOString().split('T')[0]
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Confirmed!',
                        'The appointment has been confirmed.',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'Error!',
                        'There was a problem confirming the appointment.',
                        'error'
                    );
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'There was a problem confirming the appointment.',
                    'error'
                );
            });
        }
    });
});
