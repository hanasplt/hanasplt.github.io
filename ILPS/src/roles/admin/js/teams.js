// Logout Confirmation
document.getElementById('logout').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7FD278',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log me out',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // mag redirect siya to the login page
            window.location.href = '../admin/teams.php?logout';
        }
    });
});


// Open Add Modal
function openAddModal() {
  document.getElementById('addModal').style.display = 'block';
}

  // Close Modal
  function closeModal(modalID) {
    document.getElementById(modalID).style.display = 'none';
  }

  // Handle Form Submission for Add Team
  document.getElementById('teamForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var modal = document.getElementById('teamForm');

    // Get all checkboxes inside the Edit Modal
    var checkboxes = modal.querySelectorAll('input[name="course[]"]');

    // Check if at least one checkbox is selected
    var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!atLeastOneChecked) { // Prevent form submission
        Swal.fire({
          title: 'Oops..',
          text: 'Select a course(s)!',
          icon: 'error',
          confirmButtonText: 'OK'
        })
    } else { // Proceed inserting team
      var formData = new FormData(this);

      fetch('../admin/teamsprocess.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json()) 
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            title: 'Success',
            text: data.message,
            icon: 'success',
            confirmButtonText: 'Yes'
          }).then((result) => {
            location.reload();
          });
        } else {
          Swal.fire({
            title: 'Oops!',
            text: data.message,
            icon: 'error',
            confirmButtonText: 'Yes'
          });
        }
        
      })
      .catch(error => console.error('Error:', error));
    }
  });


  // Handle Form Submission for Edit Team
  document.getElementById('editTeamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var modal = document.getElementById('editTeamForm');
    
    // Get all checkboxes inside the Edit Modal
    var checkboxes = modal.querySelectorAll('input[name="course[]"]');

    // Check if at least one checkbox is selected
    var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!atLeastOneChecked) { // Prevent form submission
        Swal.fire({
          title: 'Oops..',
          text: 'Select a course(s)!',
          icon: 'error',
          confirmButtonText: 'OK'
        })
    } else { // Proceed updating team
      var formData = new FormData(this);
    
      fetch('../admin/teamsprocess.php', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({
              title: 'Success!',
              text: data.message,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              title: 'Oops!',
              text: data.message,
              icon: 'error',
              confirmButtonText: 'OK'
            })
          }
        });
    }
  });

  $(document).on("click", ".card", function() {
    var teamId = $(this).data('id'); // Get the teamId from the clicked card
  
    // Fetch team details
    $.ajax({
      method: "POST",
      url: "../admin/teamsprocess.php",
      data: { viewTeamId: teamId },
      dataType: "json",
      success: function(response) {
        if (response.error) { // Display error message
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: response.error,
          });
        } else {
          // Display the data in the SweetAlert
          Swal.fire({
            title: response.teamName,
            html: `<div style="text-align: center;">
                     <img src="../../public/uploads/${response.teamImage}" alt="Team Image" style="width: 150px; height: 150px; object-fit: cover;">
                     <p style="margin-top: 15px;"><b>Team Members:</b><br>${response.teamMembers}</p>
                   </div>`,
            showCloseButton: true,
            showConfirmButton: false,
          });
        }
      }
    });
  });
  
  // Delete Team
  $(document).on("click", ".fa-trash", function(e) {
    e.stopPropagation(); // Prevent click event from bubbling up to the card
    var teamId = $(this).closest('.card').data('id'); // Get the teamId from the card
    var name = $(this).closest('.card').data('name'); // Get the teamname from the card
    
    Swal.fire({
      title: 'Confirm',
      text: "Do you want to delete this team?",
      icon: 'warning',
      cancelButtonColor: '#8F8B8B',
      confirmButtonColor: '#7FD278',
      confirmButtonText: 'Confirm',
      showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../admin/teamsprocess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    teamid: teamId,
                    teamname: name
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Team deleted successfully!',
                    icon: 'success',
                    confirmButtonColor: '#7FD278',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            }).catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Team is in an event; cannot be deleted',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
  });
  
  // Edit Team
  $(document).on("click", ".fa-pen", function(e) {
    e.stopPropagation(); // Prevent click event from bubbling up to the card
    var teamID = $(this).closest('.card').data('id'); // Get the teamId from the card
    var teamName = $(this).closest('.card').data('name'); // Get the teamname from the card

    document.getElementById('editTeamID').value = teamID;
    document.getElementById('editTeamName').value = teamName;
    document.getElementById('editTeamImage').value = ''; // Reset file input


    fetch(`../admin/teamsprocess.php?editID=` + teamID)
    .then(response => response.json())
    .then(data => {
        // Get only checkboxes inside the Edit Modal
        var modal = document.getElementById('editModal');

        // Reset all checkboxes
        var checkboxes = document.querySelectorAll('input[name="course[]"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false; // Uncheck all checkboxes
        });

        // Check the checkboxes that match the team's courses
        data.courses.forEach(function(course) {
            var checkbox = modal.querySelector('input[name="course[]"][value="' + course + '"]');
            if (checkbox) {
                checkbox.checked = true; // Check the matching checkbox
            }
        });
    })
    .catch(error => console.error('Error fetching course data:', error));

    document.getElementById('editModal').style.display = 'block';
  });