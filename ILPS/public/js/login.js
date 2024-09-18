document.getElementById('show-password').addEventListener('change', function() {
    var passwordField = document.getElementById('password');
    if (this.checked) {
        passwordField.type = 'text'; // when checkbox is clicked, input will be shown
    } else {
        passwordField.type = 'password'; // when not, input is shown as dots
    }
});

document.getElementById('loginform').addEventListener('submit', function(event) {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var msg = document.getElementById('msg');
    msg.innerHTML = '';

    if (!username || !password) { // error message for login validation
        event.preventDefault();
        if (!username) {
            msg.innerHTML += '<p style="color: red">Please enter your username.</p>';
        }
        if (!password) {
            msg.innerHTML += '<p style="color: red">Please enter your password.</p>';
        }
    }
});