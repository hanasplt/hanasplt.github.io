// Display form for the event selected
function showSelectedForm() {
    var selectedEvent = document.getElementById("dropdownMenuEvents").value;
    console.log('selected event: ', selectedEvent);

    if(selectedEvent != "") {
        window.location.href = "SCevents.php?event="+selectedEvent;
    }
}

// LOGOUT CONFIRMATION
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
            window.location.href = '../judge/judge.php?logout';
        }
    });
});