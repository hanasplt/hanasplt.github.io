document.getElementById('cancelBtn').addEventListener('click', function() {
    window.parent.postMessage('closePopup', '*');
});

document.getElementById('accountForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confpassword').value;

    if (password !== confirmPassword) {
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    var formData = new FormData(this);

    fetch('../create-account.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.parent.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error occurred during form submission:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while submitting the form.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

function validateInput(event) {
    // Allow only letters (both lowercase and uppercase) and spaces
    const regex = /^[a-zA-Z\s]*$/;
    const inputField = event.target;

    if (!regex.test(inputField.value)) {
        inputField.value = inputField.value.replace(/[^a-zA-Z\s]/g, '');
    }
}

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});

document.getElementById('toggleConPassword').addEventListener('click', function() {
    const conPasswordField = document.getElementById('conpassword');
    const type = conPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
    conPasswordField.setAttribute('type', type);

    const icon = this.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});