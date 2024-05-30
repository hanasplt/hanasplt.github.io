/* log out*/
function handleLogout(){
    window.location.href = "adminLogin.html";
}

    document.getElementById('logoutButton').onclick = handleLogout;

/*dashboard*/
function handleDashboard(){
    window.location.href = "dashboard.html";
}

    document.getElementById('dashButton').onclick = handleDashboard;

/*appointments*/
function handleAppointments(){
    window.location.href = "appointments.html";
}

    document.getElementById('appButton').onclick = handleAppointments;

/*reservations*/
function handleReservations(){
    window.location.href = "reservations.html";
}

    document.getElementById('reservButton').onclick = handleReservations;

/*reservations*/
function handleInventory(){
    window.location.href = "inventory.html";
}

    document.getElementById('invButton').onclick = handleInventory;

/*history*/
function handleHistory(){
    window.location.href = "history.html";
}

    document.getElementById('historyButton').onclick = handleHistory;

/*billing*/
function handleBilling(){
    window.location.href = "billing.html";
}

    document.getElementById('billButton').onclick = handleBilling;    


/*date*/
var currentDate = new Date();
var options = {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
};


var formattedDate = currentDate.toLocaleDateString('en-US', options);
document.getElementById('date').innerHTML = formattedDate;


document.addEventListener("DOMContentLoaded", function() {
    var searchBtn = document.getElementById("search-btn");

    searchBtn.addEventListener("click", function(event) {
        event.preventDefault();

        var appIdInput = document.getElementById("app-id");
        var appId = appIdInput.value.trim();

        var foundRecord = searchRecord(appId);

        if (foundRecord) {
            populateInputFields(foundRecord);
            showSuccessAlert();
        } else {
            handleNewReservationId(appId);
        }
    });

    function searchRecord(appId) {
        var records = [
            { id: "A000001", name: "Debbie Gerodias", contact_number: "09923316087", email: "debbiegerodias19@gmail.com", unit: "Acer Aspire 7", issue: "Unable to start", service_render: "Cleaning and replacement", fee: "PHP 550", date: "April 10, 2024" },
            { id: "A000003", name: "Hermann Goering", contact_number: "09093346721", email: "luftwaffe@gmail.com", unit: "Lenovo Legion Y70", issue: "Damaged camera", service_render: "camera replacement", fee: "PHP 3,500", date: "January 12, 2024" }
        ];

        return records.find(function(record) {
            return record.id === appId;
        });
    }

    function populateInputFields(record) {
        document.querySelector("input[name='name']").value = record.name;
        document.querySelector("input[name='contact_number']").value = record.contact_number;
        document.querySelector("input[name='email']").value = record.email || ""; 
        document.querySelector("input[name='unit']").value = record.unit;
        document.querySelector("input[name='issue']").value = record.issue || ""; 
        document.querySelector("input[name='service-render']").value = record.service_render || ""; 
        document.querySelector("input[name='fee']").value = record.fee || ""; 
        document.querySelector("input[name='date']").value = record.date || "";
    }

    function handleNewReservationId(appId) {
        Swal.fire({
            icon: 'info',
            title: 'ID not found!',
            text: 'The given ID will now be recorded as new.',
            confirmButtonText: 'Go Back'
        });
    }

    function showSuccessAlert() {
        Swal.fire({
            icon: 'success',
            title: 'ID found!',
            confirmButtonText: 'OK'
        });
    }
});


document.addEventListener("DOMContentLoaded", function() {
    var saveBtn = document.querySelector(".btn-save");
    var appIdInput = document.getElementById("app-id");

    saveBtn.addEventListener("click", function(event) {
        event.preventDefault();

        if (validateFields() && validateReservationId()) {
            if (document.querySelector("input[name='email']").value.trim() !== "") {
                if (!validateEmail()) {
                    showErrorAlert("Invalid Email!");
                    return;
                }
            }

            saveData();
        } else {
            showErrorAlert("Incomplete Fields or Invalid Reservation ID!");
        }
    });

    function validateFields() {
        var inputs = document.querySelectorAll(".billing-input-value3 input[type='text'], .billing-input-value4 input[type='text']");
        var isValid = true;

        inputs.forEach(function(input) {
            if (input.value.trim() === "" && input.name !== "email") {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateReservationId() {
        var appId = appIdInput.value.trim();
        var pattern = /^A\d{6}$/;
        return pattern.test(appId);
    }

    function validateEmail() {
        var emailInput = document.querySelector("input[name='email']");
        var email = emailInput.value.trim();
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }

    function saveData() {
        Swal.fire({
            icon: 'info',
            title: 'Proceed',
            confirmButtonText: 'OK',
            showCancelButton: true,
            cancelButtonText: 'Go Back'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'service-preview.html';
            }
        });
    }

    function showErrorAlert(title, message) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonText: 'OK'
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var saveBtn = document.getElementById("save-btn");
    var printBtn = document.getElementById("print-btn");
    var cancelBtn = document.getElementById("cancel-btn");

    saveBtn.addEventListener("click", function() {
        Swal.fire({
            icon: 'question',
            title: 'Save Changes?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Go Back'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully saved!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = "billing.html";
                });
            }
        });
    });

    printBtn.addEventListener("click", function() {
        Swal.fire({
            icon: 'question',
            title: 'Print Receipt?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Go Back'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Printing',
                    text: 'Printing document...',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.open("service-receipt.html", "_blank");
                    window.location.href = "billing.html";
                });
            }
        });
    });

    cancelBtn.addEventListener("click", function() {
        Swal.fire({
            icon: 'question',
            title: 'Cancel Changes?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Go Back'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'question',
                    title: 'Return to Edit?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Go Back'
                }).then((editResult) => {
                    if (editResult.isConfirmed) {
                        window.location.href = "billing-service.html";
                    }
                });
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const dash = document.getElementById('dash');
    const menu = document.querySelector('.menu');
    const icon = document.getElementById('menuIcon');

    menu.style.display = 'none';
    
    function toggleMenu() {
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            menu.style.display = 'none';
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }
    
    dash.addEventListener('click', toggleMenu);
    
    icon.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleMenu();
    });
})