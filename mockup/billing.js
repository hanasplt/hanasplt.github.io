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

        var resIdInput = document.getElementById("res-id");
        var resId = resIdInput.value.trim();

        var foundRecord = searchRecord(resId);

        if (foundRecord) {
            populateInputFields(foundRecord);
            showSuccessAlert();
        } else {
            handleNewReservationId(resId);
        }
    });

    function searchRecord(resId) {
        var records = [
            { id: "RP00001", name: "Albert Kesselring", contact_number: "09996320917", email: "ak35krieg@gmail.com", prod_id: "VIVO0001", unit: "Vivo V27 Pro", color: "Himalayan Blue", price: "PHP 26,999", quantity: "1", total_price: "PHP 26,999", payment: "GCash", paid: "PHP 26,999" },
            { id: "RA00003", name: "Nikolaus Barbie", contact_number: "09880012345", email: " ", prod_id: "PB10001", unit: "Philips 50000mAH Power Bank", color: "White", price: "PHP 3,000", quantity: "1", total_price: "PHP 3,000", payment: "Cash", paid: "PHP 3,000" }
        ];

        return records.find(function(record) {
            return record.id === resId;
        });
    }

    function populateInputFields(record) {
        document.querySelector("input[name='name']").value = record.name;
        document.querySelector("input[name='contact_number']").value = record.contact_number;
        document.querySelector("input[name='email']").value = record.email;
        document.querySelector("input[name='prod_id']").value = record.prod_id;
        document.querySelector("input[name='unit']").value = record.unit;
        document.querySelector("input[name='color']").value = record.color;
        document.querySelector("input[name='price']").value = record.price;
        document.querySelector("input[name='quantity']").value = record.quantity;
        document.querySelector("input[name='total_price']").value = record.total_price;
        document.querySelector("input[name='payment']").value = record.payment;
        document.querySelector("input[name='paid']").value = record.paid;
    }

    function handleNewReservationId(resId) {
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
    var resIdInput = document.getElementById("res-id");

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
        var resId = resIdInput.value.trim();
        var pattern = /^(RP|RA)\d{5}$/;
        return pattern.test(resId);
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
                window.location.href = 'purchase-preview.html';
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
                    window.open("purchase-receipt.html", "_blank");
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
                        window.location.href = "billing-purchase.html";
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
