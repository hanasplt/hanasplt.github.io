// TOGGLE EVENT TABLES (NAGAMIT)
function toggleEvent(divId, evType, evId, tableId) {
    console.log("Toggling table:", divId); // Checking if toggled

    const table = document.getElementById(divId);
    const tables = document.querySelectorAll('.container');

    tables.forEach(t => {
        if (t.id !== divId) {
            t.style.display = 'none';
        }
    });

    loadContestant(evId, divId);
    if (evType == "Sports") {
        loadCommittee(evId, tableId);
    } else {
        loadJudge(evId, tableId);
    }
    table.style.display = table.style.display === 'none' ? 'block' : 'none';
}

// DISPLAYING TABLE CONTESTANT
function loadContestant(evid, divId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector(divId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_contestants.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evId=" + evid);
}


// DISPLAYING TABLE COMMITTEE
function loadCommittee(evid, tableId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_faci.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid);
}


// DISPLATING TABLE JUDGE
function loadJudge(evid, tableId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_judge.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + id);
}



// DISPLAYING CONTESTANT MODAL
function openContestantModal() {
    var modal = document.getElementById("contModal");
    modal.style.display = "block";
}


// Deletes the event
function deleteThis(userId, name) { // Don't totally delete it
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this event?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    eventid: userId,
                    eventname: name
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Event deleted successfully!',
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
                    text: 'Error deleting event.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}


// Open edit event modal
function openEditEventModal(element) { // Open modal for editing event
    var card = element.closest('.account');
    var eventID = card.getAttribute('data-event-id');
    var eventType = card.getAttribute('data-type');
    var eventName = card.getAttribute('data-name');
    var eventCat= card.getAttribute('data-category');

    document.getElementById('editeventId').value = eventID;
    document.getElementById('editeventType').value = eventType;          
    document.getElementById('editeventName').value = eventName;
    document.getElementById('editeventCategory').value = eventCat;          

    var modal = document.getElementById("editEventModal");
    modal.style.display = "block";
}


// Retrieving the selected text in the adding contestant dropdown
function getSelectedText() {
    var selectElement = document.getElementById('eventId');
    var selectedText = selectElement.options[selectElement.selectedIndex].text;
    document.getElementById('selectedEventText').value = selectedText;
}
function updateNameField() {
    var dropdown = document.getElementById("contestantId");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("contestantName").value = selectedText;
}



        // Form submission for editing event
        document.querySelector('.save-btn-editev').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#editeventForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding contestant
        document.querySelector('.save-btn-contestant').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addContForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


        // Display contestant no. input field when event type = 'Socio-Cultural'
        document.getElementById('eventId').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var eventType = selectedOption.getAttribute('data-type');
            
            // Get the input field element
            var contestantNumField = document.getElementById('contestantNumField');
            
            if (eventType === 'Socio-Cultural') {
                contestantNumField.style.display = 'block';
            } else {
                contestantNumField.style.display = 'none';
            }
        });























// SHOW TABLES //

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.loadContestantsBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            loadDoc(button); //load contestant table
        });
    });

    document.querySelectorAll('.loadFacilitatorsBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            loadFaci(button); //load Committee table
        });
    });

    document.querySelectorAll('.loadJudgesBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            loadJudge(button); //load judge table
        });
    });

    document.querySelectorAll('.loadCriteriaBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            loadCriteria(button); //load criteria table
        });
    });

    document.querySelectorAll('.loadScoringBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            loadScoring(button); //load scoring table
        });
    });

    //deleting contestant
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon')) {
            var conid = event.target.getAttribute('data-cont');
            deleteCont(conid);
        }
    });

    //deleting Committee
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-faci')) {
            var id = event.target.getAttribute('data-id');
            deleteFaci(id);
        }
    });

    //deleting judge
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-judge')) {
            var id = event.target.getAttribute('data-id');
            deleteJudge(id);
        }
    });

    //deleting judge
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-cri')) {
            var id = event.target.getAttribute('data-id');
            deleteCri(id);
        }
    });

    //deleting score
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-pts')) {
            var name = event.target.getAttribute('data-rank');
            deleteScoring(name);
        }
    });
});


function loadDoc(button) {
    var id = button.getAttribute('data-event');
    var tableId = button.getAttribute('data-table-id')
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_contestants.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evId=" + id);
}

function loadFaci(button) {
    var evid = button.getAttribute('data-event');
    var evname = button.getAttribute('data-name');
    var tableId = button.getAttribute('data-table-id');
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_faci.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid);
}

function loadJudge(button) {
    var id = button.getAttribute('data-event');
    var tableId = button.getAttribute('data-table-id');
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_judge.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + id);
}

function loadCriteria(button) {
    var evid = button.getAttribute('data-event');
    var tableId = button.getAttribute('data-table-id');
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_criteria.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid);
}

function loadScoring(button) {
    var tableId = button.getAttribute('data-table-id');
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_scoring.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

// DISPLAY CORRESPONDING ROWS //

function toggleSubEvents(subEventId) { // Display events from Socio or Sports
    const subEvents = document.getElementById(subEventId);
    subEvents.style.display = subEvents.style.display === 'none' ? 'block' : 'none';
}

// Display button for contestant, committee/judge, criteria, scoring
function toggleTable(tableId) { 
    console.log("Toggling table:", tableId);
    const table = document.getElementById(tableId);
    const tables = document.querySelectorAll('.container');
    tables.forEach(t => {
        if (t.id !== tableId) {
            t.style.display = 'none';
        }
    });
    table.style.display = 'block';
}

// Display events' contestant, committee/judge, criteria, scoring
function toggleSubTable(tableId) {
    console.log("Toggling table:", tableId);
    const table = document.getElementById(tableId);
    const tables = document.querySelectorAll('.hidden-table');
    tables.forEach(t => {
        if (t.id !== tableId) {
            t.style.display = 'none';
        }
    });
    table.style.display = 'block';
}




// UPDATES //



function updateFaciName() {
    var dropdown = document.getElementById("comtId");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("comtName").value = selectedText;
}

function updateJudgeField() {
    var dropdown = document.getElementById("judgeId");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("judgeName").value = selectedText;
}



// DELETES //


function deleteCont(id) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this contestant?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    contid: id
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Contestant deleted successfully!',
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
                    text: 'Error deleting contestant.'+ error,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}


function deleteFaci(id) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this committee?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    comtid: id
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Committee deleted successfully!',
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
                    text: 'Error deleting committee.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}


function deleteJudge(id) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this judge?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    judgeid: id
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Judge deleted successfully!',
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
                    text: 'Error deleting judge.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}


function deleteCri(id) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this criteria?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    criid: id
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Criteria deleted successfully!',
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
                    text: 'Error deleting criteria.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}


function deleteScoring(name) {
    Swal.fire({
        title: 'Confirm',
        text: "Do you want to delete this scoring?",
        icon: 'warning',
        cancelButtonColor: '#8F8B8B',
        confirmButtonColor: '#7FD278',
        confirmButtonText: 'Confirm',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: new URLSearchParams({
                    rank: name
                })
            }).then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            }).then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Scoring deleted successfully!',
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
                    text: 'Error deleting scoring.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}





// MODALS //

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("openModal").addEventListener("click", function () {
        openModal();
    });
});

function openModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

function openContiModal(element) {
    var card = element.closest('.addcon-btn');
    var eventType = card.getAttribute('data-type');
    var eventName = card.getAttribute('data-name');
    var evId = card.getAttribute('data-event');

    document.getElementById('contestantType').value = eventType;          
    document.getElementById('contestantEVName').value = eventName;
    document.getElementById('conEvId').value = evId;

    var modal = document.getElementById("contestandtModal");
    modal.style.display = "block";
}

function openFaciModal(element) {
    var card = element.closest('.addfaci-btn');
    var event = card.getAttribute('data-event');
    var eventName = card.getAttribute('data-name');

    document.getElementById('comtEvId').value = event;          
    document.getElementById('comtEVName').value = eventName;

    var modal = document.getElementById("faciModal");
    modal.style.display = "block";
}

function openJudgeModal(element) {
    var card = element.closest('.addjudge-btn');
    var event = card.getAttribute('data-event');
    var eventName = card.getAttribute('data-name');

    document.getElementById('judgeEvId').value = event;          
    document.getElementById('judgeEVName').value = eventName;

    var modal = document.getElementById("judgeModal");
    modal.style.display = "block";
}

function openCriModal(element) {
    var card = element.closest('.addcri-btn');
    var eventid = card.getAttribute('data-event');

    document.getElementById('criEVId').value = eventid;

    var modal = document.getElementById("criModal");
    modal.style.display = "block";
}

function openEditCriModal(element) {
    var card = element.closest('.edit-icon-cri');
    var criId = card.getAttribute('data-id');
    var criteria = card.getAttribute('data-criteria');
    var pts = card.getAttribute('data-pts');

    document.getElementById('editcriId').value = criId;
    document.getElementById('editcriteria').value = criteria;
    document.getElementById('editcriPts').value = pts;

    var modal = document.getElementById("editcriModal");
    modal.style.display = "block";
}

function openScoreModal(element) {
    var modal = document.getElementById("scoringModal");
    modal.style.display = "block";
}

function closeModal(thisModal) {
    var modal = document.getElementById(thisModal);
    modal.style.display = "none";
}

function closeEditModal() { // Close modal on edit event
    var modal = document.getElementById("editEventModal");
    modal.style.display = "none";
}

function openModal() { // Open modal for adding event
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

function openEditEvModal(element) { // Open modal for editing event
    var card = element.closest('.sub-account');
    var eventID = card.getAttribute('data-id');
    var eventType = card.getAttribute('data-type');
    var eventName = card.getAttribute('data-name');
    var eventCat= card.getAttribute('data-category');

    document.getElementById('editeventId').value = eventID;
    document.getElementById('editeventType').value = eventType;          
    document.getElementById('editeventName').value = eventName;
    document.getElementById('editeventCategory').value = eventCat;          

    var modal = document.getElementById("editEventModal");
    modal.style.display = "block";
}







// FORM SUBMISSIONS //

        // Form submission for adding event
        document.querySelector('.save-btn-event').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addEvForm'));

            fetch('../admin/EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding committee
        document.querySelector('.save-btn-comt').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addComtForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding judge
        document.querySelector('.save-btn-judge').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addJudgeForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding criteria
        document.querySelector('.save-btn-cri').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addCriForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for editing criteria
        document.querySelector('.save-btn-editcri').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#editCriForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding score
        document.querySelector('.save-btn-scr').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            var formData = new FormData(document.querySelector('#addScoringForm'));

            fetch('EventTeamProcess.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();  // Reload the page or handle success
                    }); 
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });



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
            // mag redirect siya to the login page
            window.location.href = '../admin/EventTeam.php?logout';
        }
    });
});
        