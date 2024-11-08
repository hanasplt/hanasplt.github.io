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


/* BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */
function showConfirmationMsg() {
    // Backup and Drop database confirmation
    Swal.fire({
        title: 'Database Reset Confirmation',
        html: "<p style='text-align: left;'><b>Warning</b>: You are about to perform a database backup and reset. This will:</br>1. <b>Download a backup</b> of your current data</br>2. <b>Permanently delete</b> all records from the database</br></br>This action <b><u>cannot be undone</u></b>.</p>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7FD278',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Backup and Reset',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../backup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=backup'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Download the data
                    downloadBackup(data.file);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    })
                }
            })
            .catch(error => {
                console.log('Exception Error: '+error.message);
            })
        }
    });
}

function downloadBackup(file) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../backup.php';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'download';

    const fileInput = document.createElement('input');
    fileInput.type = 'hidden';
    fileInput.name = 'file';
    fileInput.value = file;

    form.appendChild(actionInput);
    form.appendChild(fileInput);
    document.body.appendChild(form);
    form.submit();

    setTimeout(() => {
        Swal.fire({
            title: 'Redirecting...',
            text: 'Your backup has been downloaded successfully!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        }).then(() => {
            window.location.href = '../../index.html';
        });
    }, 1000);
    
    document.body.removeChild(form);
}
/* END BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */

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