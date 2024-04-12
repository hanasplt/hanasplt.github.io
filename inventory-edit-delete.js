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


/*menu*/
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
});



function confirmDelete() {
    Swal.fire({
        title: 'Are you sure to delete?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Go Back'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Successfully Deleted!',
                confirmButtonText: 'Go Back'
            }).then(() => {
                window.location.href = 'inventory.html';
            });
        }
    });
}
document.addEventListener("DOMContentLoaded", function() {
    var saveBtn = document.querySelector(".btn-save");

    saveBtn.addEventListener("click", function(event) {
        event.preventDefault(); 

        Swal.fire({
            title: 'Save Changes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Go Back',
        }).then((result) => {
            if (result.isConfirmed) {
                var color = document.querySelector("input[name='color']").value;
                var quantity = document.querySelector("input[name='quantity']").value;
                var price = document.querySelector("input[name='price']").value;
                var specs = document.querySelector("input[name='specs']").value;
                var imageInput = document.querySelector("input[name='image']");
                var image = imageInput.files[0];
                var imageName = imageInput.value.split('\\').pop();

                if (!color || !quantity || !price || !specs || !image) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Details Incomplete!',
                        confirmButtonText: 'Go Back',
                    });
                    return; 
                }

                console.log("Form data:", {
                    color: color,
                    quantity: quantity,
                    price: price,
                    specs: specs,
                    image: imageName
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Edited!',
                    confirmButtonText: 'Yes',
                }).then(function() {
                    window.location.href = "inventory.html";
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