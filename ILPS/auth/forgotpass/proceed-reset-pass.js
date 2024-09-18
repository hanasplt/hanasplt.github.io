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


// Form validation and submission
document.querySelector('.save-btn').addEventListener('click', function(event) {
    event.preventDefault();  // Prevent default form submission

    var password = document.getElementById('newpass').value;
    var confirmPassword = document.getElementById('confpass').value;

    // Validate password and confirm password--they must match
    if (password !== confirmPassword) {
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    var myInput = document.getElementById("newpass");
    var passwordMsg = "";

    // Password Input Validation
    var lowerCaseLetters = /[a-z]/g;
    var upperCaseLetters = /[A-Z]/g;
    var numbers = /[0-9]/g;

    if (!myInput.value.match(lowerCaseLetters)) {
        passwordMsg += "Password must contain a lowercase letter.";
    }
    if (!myInput.value.match(upperCaseLetters)) {
        passwordMsg += "Password must contain an uppercase letter.";
    }
    if (!myInput.value.match(numbers)) {
        passwordMsg += "Password must contain a number.";
    }
    if (myInput.value.length < 8) {
        passwordMsg += "Password must be at least 8 characters long.";
    }

    if (passwordMsg) {
        Swal.fire({
            title: 'Message',
            text: passwordMsg,
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }

    // When password is valid and matches, proceed to form submission
    var formData = new FormData(document.querySelector('#changePasswordForm'));

    fetch('proceed-reset-pass.php', {
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
                window.location.href = '../login.php';  // Redirect to Login page
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
        Swal.fire({
            title: 'Oops!',
            text: 'An error occurred: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
