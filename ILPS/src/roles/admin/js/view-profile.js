// Display edit account form
document.querySelectorAll('.edit-button').forEach(function(editIcon) {
    editIcon.addEventListener('click', function(event) { 
        event.stopPropagation();
        
        var userId = this.getAttribute('data-user-id');
        var iframe = document.getElementById('editIframe');
        iframe.src = '../admin/html/edit-account-profile.html?userId=' + userId;
        document.getElementById('popupEdit').style.display = 'block';
    });
});

window.addEventListener("message", function(event) {
    if (event.data === "closePopup") {
        document.getElementById("popupEdit").style.display = "none";
        document.getElementById("popupEdit").style.display = "none";
    }
});


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
            window.location.href = '../admin/admin.php?logout';
        }
    });
});