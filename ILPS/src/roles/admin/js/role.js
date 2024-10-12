function showRoleDetails(id) {
    // fetch user role details from the database
}


document.addEventListener("DOMContentLoaded", function() {
    // Display edit user role form
    document.querySelectorAll('.edit-icon').forEach(function(editIcon) {
        editIcon.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var iframe = document.getElementById('popupFrame');
            iframe.src = '../admin/html/update-roles.html?userId=' + userId;
            document.getElementById('iframeOverlay').style.display = 'block';
        });
    });
    window.addEventListener("message", function(event) {
        if (event.data === "closePopup") {
            document.getElementById("iframeOverlay").style.display = "none";
        }
    });
    
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
                window.location.href = '../admin/admin.php?logout';
            }
        });
    });
});