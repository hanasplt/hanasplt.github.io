function showSelectedForm() { // Display form for the event selected
    var dropdownMenu = document.getElementById("dropdownMenuEvents");
    var selectedEvent = dropdownMenu.value;
    var evName = dropdownMenu.options[dropdownMenu.selectedIndex].text;

    if(selectedEvent != "") {
        window.location.href = "Sevents.php?event="+selectedEvent+"&name="+evName;
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
            // mag redirect siya to the login page
            window.location.href = '../committee/committee.php?logout';
        }
    });
});