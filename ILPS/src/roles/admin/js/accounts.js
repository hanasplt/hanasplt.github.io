// Display create account form
document.getElementById("openPopup").addEventListener("click", function() { 
    document.getElementById("popupFrame").src = "../admin/html/create-account.html";
    document.getElementById("iframeOverlay").style.display = "block";
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
            // mag redirect siya to the login page
            window.location.href = '../admin/admin.php?logout';
        }
    });
});


// Search functionality
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}
// Search functionality
document.getElementById('searchBox').addEventListener('input', debounce(function() {
    var searchValue = this.value;
    if (searchValue.length > 0) {
        window.location.href = '?search=' + encodeURIComponent(searchValue) + '&page=1';
    } else {
        window.location.href = '?page=1';
    }
}, 500));

// Display edit account form
document.querySelectorAll('.edit-icon').forEach(function(editIcon) {
    editIcon.addEventListener('click', function(event) { 
        event.stopPropagation();
        
        var userId = this.getAttribute('data-user-id');
        var iframe = document.getElementById('editIframe');
        iframe.src = '../admin/html/edit-account.html?userId=' + userId;
        document.getElementById('popupEdit').style.display = 'block';
    });
});

window.addEventListener("message", function(event) {
    if (event.data === "closePopup") {
        document.getElementById("popupEdit").style.display = "none";
        document.getElementById("popupEdit").style.display = "none";
    }
});


// delete confirmation
function confirmDelete(userId, name) {
    event.stopPropagation(); 
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


var accountsContainer = document.querySelector('.accounts');
var originalAccounts = Array.from(accountsContainer.querySelectorAll('.account'));

// sort alphabetically
document.getElementById('abc').addEventListener('change', function() { 
    var sortOrder = this.value;
    var accounts = Array.from(originalAccounts);

    if (sortOrder === 'a-z') {
        accounts.sort(function(a, b) {
            var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
            var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
            return nameA.localeCompare(nameB); // sort A-Z
        });
    } else if (sortOrder === 'z-a') {
        accounts.sort(function(a, b) {
            var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
            var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
            return nameB.localeCompare(nameA); // sort Z-A
        });
    } else {
        accounts = originalAccounts; // show original order
    }
    
    accounts.forEach(function(account) {
        accountsContainer.appendChild(account);
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