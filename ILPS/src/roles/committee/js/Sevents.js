// DISPLAY SCORE FORM
function displayForm() {
    var form = document.getElementById('scoreForm');
    form.style.display = 'block';
}
// DISPLAY TALLY TABLE
function displayTally() {
    var form = document.getElementById('TallyTable');
    form.style.display = 'block';
}

var x = document.getElementById('x-btn');
if (x) {
    x.addEventListener('click', function() {
        const msg = document.getElementById('msg-container');

        msg.style.display = "none";
    });
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
            window.location.href = '../committee/Sevents.php?logout';
        }
    });
});


