$(document).on("click", "#addMore", function(e) {
    e.preventDefault();

    // Add more row of input
    $(".items").append(`
        <div class="row additionalRow data-row">
            <div class="col-7">
                <input type="text" class="form-control" name="criteria[]" required>
            </div>
            <div class="col-3">
                <input type="number" class="form-control" id="criPts" name="criPts[]" required>
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

function updateCriteriaField() {
    var dropdown = document.getElementById("eventIdC");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("eventname").value = selectedText;
}

// Validate max and min of a criteria points
document.getElementById('criPts').addEventListener('input', function(event) {
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

$(document).on("submit", "#addCriForm", function(e) {
    e.preventDefault();

    updateCriteriaField();

    // Submits the form
    $.ajax({
        method: "post",
        url: "../admin/EventTeamProcess.php",
        data: $(this).serialize(),
        success: function(response) {
            if (response == "success") {
                // Display success message
                var str = '<div class="alert alert-success">Criteria added successfully!</div>';
                $(".additionalRow").remove();
                $("#addCriForm")[0].reset();
            } else { // Display error message
                var str = '<div class="alert alert-danger">'+response+'!</div>';
            }
            $("#msg").html(str);
        }
    });
});

// When cancel button is clicked
document.getElementById('criteria-cancelBtn').addEventListener('click', function() {
    window.parent.postMessage('closePopup', '*');
});