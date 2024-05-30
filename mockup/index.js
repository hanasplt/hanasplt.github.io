function handleLogin() {
    var username = document.getElementsByName('username')[0].value;
    var password = document.getElementsByName('password')[0].value;

    if (username == 'admin' && password == 'password') {
        window.location.href = 'dashboard.html';
    } else {
        alert('Incorrect username or password');
    }
}

document.getElementById('loginButton').onclick = handleLogin;

function togglePasswordVisibility() {
    var passwordField = document.getElementById("passwordField");
    var eyeIcon = document.getElementById("togglePassword");

    if (passwordField.type === "text") {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}


