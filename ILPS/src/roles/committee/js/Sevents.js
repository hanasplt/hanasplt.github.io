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
            window.location.href = '../committee/Sevents.php?logout';
        }
    });
});


// FORM SUBMISSION
document.addEventListener('DOMContentLoaded', function() {
    // Form submission for adding score
    document.querySelector('.save-btn-event').addEventListener('click', function(event) {
        event.preventDefault();  // Prevent default form submission

        var formData = new FormData(document.querySelector('#addEvForm'));

        fetch('../committtee/SeventsProcess.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();  // Reload the page or handle success
                }); 
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.log('An error occurred: ' + error.message);
        });
    });
});