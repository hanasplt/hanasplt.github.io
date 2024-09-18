// FOR OPENING THE OVERLAYED POP UP (CREATE ACCOUNTS)
document.getElementById("openPopup").addEventListener("click", function() {
    document.getElementById("popupFrame").src = "../admin/html/create-account.html";
    document.getElementById("iframeOverlay").style.display = "block";
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
            window.location.href = 'index.html';
        }
    });
});