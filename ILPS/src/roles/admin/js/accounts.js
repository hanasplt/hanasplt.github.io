// Display create account form
document.getElementById("openPopup").addEventListener("click", function() { 
    document.getElementById("popupFrame").src = "../admin/html/create-account.html";
    document.getElementById("iframeOverlay").style.display = "block";
});

// Use event delegation for edit icons by attaching the listener to a parent element
// that exists when the page loads
document.querySelector('.accounts').addEventListener('click', function(event) {
    // Find if the clicked element or its parent is an edit-icon
    const editIcon = event.target.closest('.edit-icon');
    if (editIcon) {
        event.stopPropagation();
        console.log("Edit icon clicked");
        
        const userId = editIcon.getAttribute('data-user-id');
        const iframe = document.getElementById('editIframe');
        iframe.src = '../admin/html/edit-account.html?userId=' + userId;
        document.getElementById('popupEdit').style.display = 'block';
    }
});

window.addEventListener("message", function(event) {
    if (event.data === "closePopup") {
        document.getElementById("iframeOverlay").style.display = "none";
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
            window.location.href = '../admin/admin.php?logout';
        }
    });
});

// delete confirmation using event delegation
document.querySelector('.accounts').addEventListener('click', function(event) {
    const deleteButton = event.target.closest('.trash-icon');
    if (deleteButton) {
        event.stopPropagation();
        const userId = deleteButton.closest('form').querySelector('input[name="userId"]').value;
        const name = deleteButton.closest('.account').querySelector('#name').textContent.trim();
        confirmDelete(userId, name);
    }
});

function confirmDelete(userId, name) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this account?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete-account.php', {
                method: 'POST',
                body: new URLSearchParams({
                    userId: userId,
                    username: name
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Account deleted successfully.',
                    icon: 'success',
                    confirmButtonColor: '#7FD278',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            }).catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error deleting account.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// sort by account type
document.getElementById('sort-type').addEventListener('change', function() { 
    var selectedType = this.value;
    var accounts = document.querySelectorAll('.account');

    accounts.forEach(function(account) {
        var type = account.querySelector('.acc-deets p:last-child').textContent.trim();

        if (selectedType === 'all' || type === selectedType) {
            account.style.display = 'flex';
        } else {
            account.style.display = 'none';
        }
    });
});




// Validate form submission for export
function submitForm(actionUrl) {
    // Set the form action to the specified file
    document.getElementById('exportForm').action = actionUrl;

    // Submit the form
    document.getElementById('exportForm').submit();
}

// Show account details
function showRoleDetails(firstName, middleName, lastName, suffix, email, type) {
    Swal.fire({
        title: 'Account Details',
        html: `
            <div class="swal-content-acc">
                <div class="column-1">
                    <label>First Name</label>
                    <p>${firstName}</p>

                    <label>Last Name</label>
                    <p>${lastName}</p>

                    <label>Email</label>
                    <p>${email}</p>
                </div>
                <div class="column-2">
                    <label>Middle Name</label>
                    <p>${middleName || 'N/A'}</p>

                    <label>Suffix</label>
                    <p>${suffix || 'N/A'}</p>

                    <label>Type</label>
                    <p>${type}</p>
                </div>
            </div>
        `,
        confirmButtonText: 'Close',
        customClass: {
            popup: 'custom-swal-popup'
        }
    });
}


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