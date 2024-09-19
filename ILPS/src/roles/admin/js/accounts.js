// Display create account form
document.getElementById("openPopup").addEventListener("click", function() { 
    document.getElementById("popupFrame").src = "../admin/html/create-account.html";
    document.getElementById("iframeOverlay").style.display = "block";
});
window.addEventListener("message", function(event) {
    if (event.data === "closePopup") {
        document.getElementById("iframeOverlay").style.display = "none";
    }
});

//LOGOUT CONFIRMATION
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
            window.location.href = '../admin/accounts.php?logout';
        }
    });
});


// Search functionality
document.getElementById('searchBox').addEventListener('input', function() {
    var searchValue = this.value.toLowerCase();
    var accounts = document.querySelectorAll('.account');

    accounts.forEach(function(account) {
        var name = account.getAttribute('data-name');
        if (name.includes(searchValue)) {
            account.style.display = 'flex';
        } else {
            account.style.display = 'none';
        }
    });
});

// Display edit account form
document.querySelectorAll('.edit-icon').forEach(function(editIcon) {
    editIcon.addEventListener('click', function() {
        var userId = this.getAttribute('data-user-id');
        var iframe = document.getElementById('editIframe');
        iframe.src = '../admin/html/edit-account.html?userId=' + userId;
        document.getElementById('popupEdit').style.display = 'block';
    });
});

window.addEventListener("message", function(event) {
    if (event.data === "closePopup") {
        document.getElementById("popupEdit").style.display = "none";
    }
});


// delete confirmation
function confirmDelete(userId) {
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
                    userId: userId
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


// sort alphabetically
document.getElementById('abc').addEventListener('change', function() { 
    var sortOrder = this.value;
    var accountsContainer = document.querySelector('.accounts');
    var accounts = Array.from(accountsContainer.querySelectorAll('.account'));

    if (sortOrder === 'a-z') {
        accounts.sort(function(a, b) {
            var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
            var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
            if (nameB < nameA) return 1;
            if (nameB > nameA) return -1;
            return 0;
        });
    } else if(sortOrder === 'z-a') {
        accounts.sort(function(a, b) {
            var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
            var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
            if (nameB < nameA) return -1;
            if (nameA > nameB) return 1;
            return 0;
        });
    }

    accounts.forEach(function(account) {
        accountsContainer.appendChild(account);
    });
});