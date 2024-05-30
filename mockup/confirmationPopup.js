function showAlert(buttonName) {
    if (buttonName === 'yes') {
        alert('Appointment confirmed!');
    } else if (buttonName === 'back') {
        alert('Going back...');
    }
}

document.getElementById('yesButton').addEventListener('click', function() {
    showAlert('yes');
    
    window.top.location.href = "appointments.html";
});

document.getElementById('goBackButton').addEventListener('click', function() {
    showAlert('back');
    window.top.location.href = "appointmentsForm.html";
});
