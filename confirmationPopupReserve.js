function showAlert(buttonName) {
    if (buttonName === 'yes') {
        alert('Reservation confirmed!');
    } else if (buttonName === 'back') {
        alert('Going back...');
    }
}

document.getElementById('yesButton').addEventListener('click', function() {
    showAlert('yes');

    window.top.location.href = "reservations.html";
});

document.getElementById('goBackButton').addEventListener('click', function() {
    showAlert('back');

    window.top.location.href = "reservations.html";
});
