$(document).on("click", "#addMore", function(e) {
    e.preventDefault();

    // Add more row of input
    $(".items").append(`
        <div class="row additionalRow data-row">
            <div class="col-7">
                <input type="text" class="form-control" name="editcriteria[]" required>
            </div>
            <div class="col-3">
                <input type="number" class="form-control" id="editcriPts" name="editcriPts[]" required>
            </div>
            <div class="col-1 remove-btn">
                <button type="button" id="remove" class="btn btn-danger" title="Remove this row">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    `);
});

$(document).on("click", "#remove", function(e) {
    e.preventDefault();

    $(this).parent().parent().remove();
});

// Validate max and min of a criteria points
document.getElementById('editcriPts').addEventListener('input', function(event) {
    const input = event.target.value;

    // Check if the input is between 0 and 100
    if (input < 1 || input > 100) {
        Swal.fire({
            title: 'Oops!',
            text: 'Please enter a value between 1 and 100.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        event.target.value = '';  // Clear the input if invalid
    }
});

$(document).on("submit", "#editCriForm"+get_eventid, function(e) {
    e.preventDefault();

    // Submits the form
    $.ajax({
        method: "post",
        url: "../admin/EventTeamProcess.php",
        data: $(this).serialize(),
        success: function(response) {
            if (response == "success") {
                // Display success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Criteria updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to Events page
                    window.top.location.href = "../admin/EventTeam.php";
                }); 
            } else { // Display error message
                var str = '<div class="alert alert-danger">'+response+'!</div>';
            }
            $("#msg").html(str);
        }
    });
});

// When cancel button is clicked
document.getElementById('editcriteria-cancelBtn'+get_eventid).addEventListener('click', function() {
    window.parent.postMessage('closePopup', '*');
});