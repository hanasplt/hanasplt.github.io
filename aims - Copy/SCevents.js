function loadEventCriteria(eventId) { // Display the Event's Criteria
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('criteriaForm').innerHTML = this.responseText;
            document.getElementById('criteriaForm').style.display = 'block';
            //addCriteriaFormEventListener();
        }
    };
    xhttp.open("POST", "get_eventFrom copy.php", true); // Retrieve display in this file
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + eventId); // Send the event id for sql query
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

    
    /*var areScoresEqual = checkIfScoresAreEqual();
    if (areScoresEqual) {
        // Blank the input and total field
        input.value = "";
        totalScoreInput.value = "";

        // Display this warning, so user will change the score
        Swal.fire({
            title: "Oops!",
            text: "There's a tie! Please break the tie by changing the score.",
            icon: "warning",
            confirmButtonText: "OK"
        });
        return;
    }*/
}


/*
function checkIfScoresAreEqual() {
    var totalScoreElements = document.querySelectorAll('[id^="totalScore"]');
    
    if (totalScoreElements.length === 0) {
        return; // No totalScore inputs found
    }
    
    // Get the first total score to compare with the others
    var firstScore = parseInt(totalScoreElements[0].value);
    
    // Iterate through all total scores and compare them
    for (var i = 1; i < totalScoreElements.length; i++) {
        var currentScore = parseInt(totalScoreElements[i].value);
        
        // If the current score is not equal to the first one, return false
        if (currentScore !== firstScore) {
            return false;
        } else {
            return true; // If score/s is/are similar, return true
        }
    }
}*/


// LOGOUT CONFIRMATION
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
            // User will be redirected to the login page
            window.location.href = 'index.html';
        }
    });
});


document.getElementById('criteriaForm').addEventListener('submit', function(event) {
    event.preventDefault();
    

    // Validate the total score for tie
    let txt = "";
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
            txt = `There is a tie with score: ${score}`;

            totalScoreArr.length = 0; // Empty the array to validate again
            break; // Exit the loop once a tie is found
        }
    }

    if (tieDetected) {
        Swal.fire({
            title: 'Oops...',
            text: "There is a tie! Break the tie by changing the score.",
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else {
        //TBC
        fetch ('')
    }


    /*var inputs = document.querySelectorAll('.criteriaInput');
    var total = 0;
    inputs.forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalScore').value = total;*/
}); 


