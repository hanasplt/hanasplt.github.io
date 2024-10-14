function showRoleDetails(id) {
    // fetch user role details from the database
    fetch('../admin/get_userPermissions.php', {
        method: 'POST',
        body: new URLSearchParams({
            userId: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status == 'success') {
            // Display user's information - name & role
            Swal.fire({
                html: `
                    <div style="width: 100%; text-align: left;">
                        <p style="text-align: left;">Name: <b>${data.fullname}</b></p>
                        <p style="text-align: left;">Role: <b>${data.type}</b></p>
                        <i style="font-size: 14px;">Click the edit icon to view ${data.fullname}'s access rights.</i>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false
            });
        } else {
            // Display error message
            Swal.fire({
                title: 'Oops!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => console.error('Error:', error));
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