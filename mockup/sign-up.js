function validate_required(field, alerttxt) {
    with (field) {
        if (value == null || value == "") {
            alert(alerttxt);
            return false;
        } else {
            return true;
        }
    }
   }
   
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
   
   function toggleConfirmPasswordVisibility() {
    var passwordInput = document.getElementById("confirm");
    var togglePasswordButton = document.querySelector(".eye-image-confirm");
   
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