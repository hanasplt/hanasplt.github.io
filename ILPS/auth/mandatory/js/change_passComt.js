// message prompt redirection
var x = document.getElementById('x-btn'); // OK button for redirection
var fid = document.getElementById('fid'); // Element where the Committee ID exists

if (x) {
    const form = document.getElementById('form-container');

    x.addEventListener('click', function() {
        // redirect to committee page
        window.location.href = "../../src/roles/committee/committee.php?id="+fid.value;
    });
}

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
        event.preventDefault();
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match!',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
});


// PASSWORD VALIDATION
document.addEventListener('DOMContentLoaded', function() {
    const newpassInput = document.getElementById('newpass');
    const letter = document.getElementById('letter');
    const capital = document.getElementById('capital');
    const number = document.getElementById('number');
    const length = document.getElementById('length');

    newpassInput.addEventListener('input', function() {
        const value = newpassInput.value;
        const lowercaseRegex = /[a-z]/;
        const uppercaseRegex = /[A-Z]/;
        const numberRegex = /[0-9]/;
        const minLength = 8;

        // Check for lowercase letter
        if (lowercaseRegex.test(value)) {
            letter.innerHTML = '✔ <i>Lowercase</i> letter';
            letter.classList.remove('invalid');
            letter.classList.add('valid');
        } else if (value.length > 0) {
            letter.innerHTML = '✘ <i>Lowercase</i> letter';
            letter.classList.remove('valid');
            letter.classList.add('invalid');
        } else {
            letter.innerHTML = '- <i>Lowercase</i> letter';
            letter.classList.remove('valid', 'invalid');
        }

        // Check for uppercase letter
        if (uppercaseRegex.test(value)) {
            capital.innerHTML = '✔ <i>Capital</i> letter';
            capital.classList.remove('invalid');
            capital.classList.add('valid');
        } else if (value.length > 0) {
            capital.innerHTML = '✘ <i>Capital</i> letter';
            capital.classList.remove('valid');
            capital.classList.add('invalid');
        } else {
            capital.innerHTML = '- <i>Capital</i> letter';
            capital.classList.remove('valid', 'invalid');
        }

        // Check for number
        if (numberRegex.test(value)) {
            number.innerHTML = '✔ A <i>Number</i>';
            number.classList.remove('invalid');
            number.classList.add('valid');
        } else if (value.length > 0) {
            number.innerHTML = '✘ A <i>Number</i>';
            number.classList.remove('valid');
            number.classList.add('invalid');
        } else {
            number.innerHTML = '- A <i>Number</i>';
            number.classList.remove('valid', 'invalid');
        }

        // Check for length
        if (value.length >= minLength) {
            length.innerHTML = '✔ A Minimum of <i>8 characters</i>';
            length.classList.remove('invalid');
            length.classList.add('valid');
        } else if (value.length > 0) {
            length.innerHTML = '✘ A Minimum of <i>8 characters</i>';
            length.classList.remove('valid');
            length.classList.add('invalid');
        } else {
            length.innerHTML = '- A Minimum of <i>8 characters</i>';
            length.classList.remove('valid', 'invalid');
        }


        // Enable confirm password input if new password is valid
        const confirmPassInput = document.getElementById('confpass');
        if (value.length >= 8 && lowercaseRegex.test(value) && uppercaseRegex.test(value) && numberRegex.test(value)) {
            confirmPassInput.disabled = false;
        } else {
            confirmPassInput.disabled = true;
        }
    });
});