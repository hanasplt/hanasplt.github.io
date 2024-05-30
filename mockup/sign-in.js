function togglePasswordVisibility() {
 var passwordInput = document.getElementById("myPass");
 var togglePasswordButton = document.querySelector(".eye-image");

 if (passwordInput.type === "password") {
     passwordInput.type = "text";
     togglePasswordButton.src = "icons/eye-open.svg";
 } else {
     passwordInput.type = "password";
     togglePasswordButton.src = "icons/eye-close.svg";
 }
 setTimeout(function() {
     passwordInput.type = "password";
     togglePasswordButton.src = "icons/eye-close.svg";
 }, 2000);
}

let usernameInput = document.getElementById("myUser");
let passwordInput = document.getElementById("myPass");
let signin = document.getElementById("signIn");

signin.addEventListener('submit', function(event) {
  event.preventDefault();
  let username = usernameInput.value.trim();
  let password = passwordInput.value.trim();
  let isValid = true;

 if (username === "" && password === "") {
    alert("Please enter both username and password.");
    isValid = false;
 } else {
     window.location.href = 'cp-homepage.html';
 }
});