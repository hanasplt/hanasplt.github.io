function loadEventCriteria(eventId, evname) { // Display the Event's Criteria
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('criteriaForm').innerHTML = this.responseText;
            document.getElementById('criteriaForm').style.display = 'block';
            //addCriteriaFormEventListener();
        }
    };
    xhttp.open("POST", "../judge/get_eventFrom.php", true); // Retrieve display in this file
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + eventId + "&evname=" + evname); // Send the event id for sql query
}



const totalScoreArr = []; // Initialize array for tie verification

function calculateTotal(criInput) { // Automatic total calculation
    // Get score inputted
    var criteriaInputs = document.getElementsByClassName('criteriaInput'+criInput);
    var totalScore = 0; // Initialize for automatic display of Total score

    for (var i = 0; i < criteriaInputs.length; i++) {
        var input = criteriaInputs[i];

        if (input.value === '') { // Validate if there's no input
            return;
        }

        var score = parseInt(input.value);
        var max = parseInt(input.getAttribute('max'));

        if (score > max) { // Score validation, if exceed
            input.value = "";
            Swal.fire({
                title: "Oops!",
                text: "You have exceeded the maximum score.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        if (score < 0) { // Score validation, if not an accurate score
            input.value = "";
            Swal.fire({
                title: "Oops...",
                text: "Invalid score!",
                icon: "error",
                confirmButtonText: "OK"
            });
            return;
        }

        totalScore += score; // Add every inputted score to display total
    }

    // Display total score for a certain contestant
    totalScoreInput = document.getElementById('totalScore'+criInput);
    totalScoreInput.value = totalScore;

    // Insert in an array the total of each contestant for tie verification
    totalScoreArr.push(totalScoreInput.value);
}

// LOGOUT CONFIRMATION
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
            // User will be redirected to the login page
            window.location.href = '../judge/SCevents.php?logout';
        }
    });
});

var criteriaform = document.getElementById('criteriaForm');
if (criteriaform) {
    document.getElementById('criteriaForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        var userid = document.getElementById('user_ID').value;
        var formData = new FormData(document.querySelector('#criteriaForm'));
    
        // Validate the total score for tie
        let tieDetected = false;
        const scoreCount = {}; // Object to track the frequency of each score
    
        // Iterate through the scores and count the occurrences
        totalScoreArr.forEach(value => {
            scoreCount[value] = (scoreCount[value] || 0) + 1;
        });
    
        // Check if any score occurs more than once, indicating a tie
        for (let score in scoreCount) {
            if (scoreCount[score] > 1) {
                tieDetected = true;
    
                totalScoreArr.length = 0; // Empty the array to validate again
                break; // Exit the loop once a tie is found
            }
        }
    
        if (tieDetected) { // Display sweetalert when there's a tie
            Swal.fire({
                title: 'Oops...',
                text: "There is a tie! Break the tie by changing the score.",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else { // Proceeding recording score
            Swal.fire({
                title: 'Are you sure?',
                text: "Please note that this action is irreversible! Your score is final and cannot be changed.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed they want to proceed recording the score
                    fetch ('../judge/SCeventsProcess.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Display success message
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '../judge/judge.php?id=' + userid; // Reload the page or handle success
                            }); 
                        } else {
                            // Display error message on insertion
                            Swal.fire({
                                title: 'Oops!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.log('Error ' + error.message);
                    });
                }
            });
        }
    });  
}


