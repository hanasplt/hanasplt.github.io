// Logout Confirmation
document.getElementById('logoutIcon').addEventListener('click', function() {
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


// Delete team
function deleteThis(id) {
    console.log(id);
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
            fetch('teamsprocess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    teamid: id
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
}


// Open Add Modal
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
  }

  // Open Edit Modal
  function openEditModal(element) {
    var card = element.closest('.card');
    var teamID = card.getAttribute('data-id');
    var teamName = card.getAttribute('data-name');
    var teamImage = card.getAttribute('data-image');
    
    document.getElementById('editTeamID').value = teamID;
    document.getElementById('editTeamName').value = teamName;
    document.getElementById('editTeamImage').value = ''; // Reset file input
    document.getElementById('editModal').style.display = 'block';
  }

  // Close Modal
  function closeModal(modalID) {
    document.getElementById(modalID).style.display = 'none';
  }

  // Handle Form Submission for Add Team
  document.getElementById('teamForm').addEventListener('submit', function(e) {
    e.preventDefault();
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
  });


  // Handle Form Submission for Edit Team
  document.getElementById('editTeamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('../teamsprocess.php', {
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
  });