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
