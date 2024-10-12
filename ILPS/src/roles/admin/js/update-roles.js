document.getElementById('cancelBtn').addEventListener('click', function() {
    window.parent.postMessage('closePopup', '*');
});

// Search functionality
function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Fetch user's permissions
document.addEventListener("DOMContentLoaded", function() {
    var userId = getQueryParam('userId');
    if (userId) {
        document.getElementById('userId').value = userId;

        fetch('../getPermissions.php?userId='+ userId)
        .then(response => response.json())
        .then(data => {
            if (data.status != 'error') {
                // Check user's role
                var role = data.type;

                // Set the role of the user in the hidden input
                document.getElementById('userRole').value = role;

                if (role == 'Admin') {
                    // Get only checkboxes inside the Admin Container
                    var modal = document.getElementById('adminPermissions');

                    // Display admin permissions container
                    modal.style.display = 'block';
            
                    // Reset all checkboxes
                    var checkboxes = document.querySelectorAll('input[name="admin_permissions[]"]');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false; // Uncheck all checkboxes
                    });
            
                    // Check the checkboxes that match the user's permissions
                    data.permissions.forEach(function(rights) {
                        var checkbox = modal.querySelector('input[name="admin_permissions[]"][value="' + rights + '"]');
                        if (checkbox) {
                            checkbox.checked = true; // Check the matching checkbox
                        }
                    });
                } else if (role == 'Committee') {
                    // Get only checkboxes inside the Committee Container
                    var modal = document.getElementById('committeePermissions');

                    // Display committee permissions container
                    modal.style.display = 'block';
            
                    // Reset all checkboxes
                    var checkboxes = document.querySelectorAll('input[name="committee_permissions[]"]');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false; // Uncheck all checkboxes
                    });
            
                    // Check the checkboxes that match the user's permissions
                    data.permissions.forEach(function(rights) {
                        var checkbox = modal.querySelector('input[name="committee_permissions[]"][value="' + rights + '"]');
                        if (checkbox) {
                            checkbox.checked = true; // Check the matching checkbox
                        }
                    });
                } else if (role == 'Judge') {
                    // Get only checkboxes inside the Judge Container
                    var modal = document.getElementById('judgePermissions');

                    // Display Judge permissions container
                    modal.style.display = 'block';
            
                    // Reset all checkboxes
                    var checkboxes = document.querySelectorAll('input[name="judge_permissions[]"]');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false; // Uncheck all checkboxes
                    });
            
                    // Check the checkboxes that match the user's permissions
                    data.permissions.forEach(function(rights) {
                        var checkbox = modal.querySelector('input[name="judge_permissions[]"][value="' + rights + '"]');
                        if (checkbox) {
                            checkbox.checked = true; // Check the matching checkbox
                        }
                    });
                }
            } else { // There is an error while fetching the php file
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            
        })
        .catch(error => { // There is an error while fetching the php file
            console.log(error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while fetching user data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }
});

// Update user rights - clicked
document.getElementById('editRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var modal = document.getElementById('editRoleForm');
    var role = document.getElementById('userRole');
    var checkboxes = ""; // Store which checkboxes to check

    // Get all checkboxes inside the container
    if (role.value == 'Admin') {
        // Get checkboxes from admin container
        checkboxes = modal.querySelectorAll('input[name="admin_permissions[]"]');
    } else if (role.value == 'Committee') {
        // Get checkboxes from committee container
        checkboxes = modal.querySelectorAll('input[name="committee_permissions[]"]');
    } else if (role.value == 'Judge') {
        // Get checkboxes from judge container
        checkboxes = modal.querySelectorAll('input[name="judge_permissions[]"]');
    }

    // To check if atleast one is selected
    var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    // Prevent form submission
    if (!atLeastOneChecked) {
        // Display error message
        Swal.fire({
          title: 'Oops..',
          text: 'Select a Access Right/s!',
          icon: 'error',
          confirmButtonText: 'OK'
        })
    } else {
        // Proceed form submission
        var formData = new FormData(this);

        fetch('../update-role.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 'success') {
                // Display success message
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload(); // Reload page
                });
            } else {
                // Display error message
                Swal.fire({
                    title: 'Oops!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            }
        })
        .catch(error => { // There is an error while fetching the php file
            console.log(error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while updating user data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }
});