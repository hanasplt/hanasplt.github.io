// SEE, UNSEE PASSWORD ENTERED
document.getElementById('toggleNewPass').addEventListener('click', function() {
    const passwordField = document.getElementById('newpass');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
document.getElementById('toggleConfPass').addEventListener('click', function() {
    const passwordField = document.getElementById('confpass');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});


// PASSWORD VALIDATION
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