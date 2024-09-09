// Show Password icon
document.getElementById('toggleNewPass').addEventListener('click', function() {
    const passwordField = document.getElementById('newpass');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});

// Show Password icon
document.getElementById('toggleConfPass').addEventListener('click', function() {
    const passwordField = document.getElementById('confpass');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});


// Form validation before submitting (password and confirm password must match)
document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    var password = document.getElementById('newpass').value;
    var confirmPassword = document.getElementById('confpass').value;

    if (password !== confirmPassword) {
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
});



// Form submission for submitting change password
document.querySelector('.save-btn').addEventListener('click', function(event) {
    event.preventDefault();  // Prevent default form submission

    var formData = new FormData(document.querySelector('#changePasswordForm'));

    fetch('process.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'login.php';  // Reload the page or handle success
            }); 
        } else {
            Swal.fire({
                title: 'Oops!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error.message);
    });
});