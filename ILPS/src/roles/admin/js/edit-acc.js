// Search functionality
function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}


// Fetch account information
document.addEventListener('DOMContentLoaded', function() {
    var userId = getQueryParam('userId');
    if (userId) {
        document.getElementById('userId').value = userId;

        fetch('../get-account.php?userId=' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'error') { // Display account information
                    document.getElementById('userId').value = data.userId;
                    document.getElementById('firstName').value = data.firstName;
                    document.getElementById('middleName').value = data.middleName;
                    document.getElementById('lastName').value = data.lastName;
                    document.getElementById('suffix').value = data.suffix;
                    document.getElementById('email').value = data.email;
                    document.getElementById('password').value = data.password;

                    var passfield = document.getElementById('password');
                    var passlabel = document.getElementById('passlabel');

                    /* If login status of account is finish
                        not even the admin will know his/her password
                        because it wont be displayed for privacy & security purposes */
                    if(data.log_status == "finish" || data.type == "Admin") {
                        passfield.style.display = 'none';
                        passlabel.style.display = 'none';
                    }

                    document.getElementById('sort').value = data.type;
                } else { // There is an error while fetching the php file
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => { // There is an error while fetching the php file
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while fetching user data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }
});


// Close edit form
document.getElementById('cancelBtn').addEventListener('click', function() {
    window.parent.postMessage('closePopup', '*');
});


// Update account information
document.getElementById('accountForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('../edit-account.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') { // Updated Successfully
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.parent.location.reload();
                });
            } else { // There is an error while fetching the php file
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => { // There is an error while fetching the php file
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while submitting the form.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
});


// Disable numerical and special character value input in the name fields
function validateInput(event) {
    // Allow only letters (both lowercase and uppercase) and spaces
    const regex = /^[a-zA-Z\s]*$/;
    const inputField = event.target;

    if (!regex.test(inputField.value)) {
        inputField.value = inputField.value.replace(/[^a-zA-Z\s]/g, '');
    }
}