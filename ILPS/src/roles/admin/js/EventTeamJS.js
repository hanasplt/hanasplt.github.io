// TOGGLE EVENT TABLES (NAGAMIT)
function toggleEvent(divId, evType, evId, evName, contTable, otherTable, criTable) {
    const table = document.getElementById(divId);
    const tables = document.querySelectorAll('.container');

    tables.forEach(t => {
        if (t.id !== divId) {
            t.style.display = 'none';
        }
    });


    if (evType == "Sports") {
        loadContestantSp(evId, contTable, evName);
        loadCommittee(evId, otherTable, evName);
    } else {
        loadContestantSc(evId, contTable, evName);
        loadJudge(evId, otherTable, evName);
        loadCriteria(evId, criTable, evName);
    }
    table.style.display = table.style.display === 'none' ? 'block' : 'none';
}


// DISPLAYING TABLE CONTESTANT (Sports)
function loadContestantSp(evid, divId, evName) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + divId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_contestants.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evId=" + evid + "&&type=Sports&&eventname=" + evName);
}
// DISPLAYING TABLE CONTESTANT (Socio-Cultural)
function loadContestantSc(evid, divId, evName) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + divId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_contestants.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evId=" + evid + "&&type=Socio-Cultural&&eventname=" + evName);
}
// DISPLAYING TABLE COMMITTEE
function loadCommittee(evid, tableId, evName) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_committee.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid + "&&eventname=" + evName);
}
// DISPLATING TABLE JUDGE
function loadJudge(evid, tableId, evName) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_judge.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid + "&&eventname=" + evName);
}
// DISPALYING TABLE CRITERIA
function loadCriteria(evid, tableId, evName) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + tableId).innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_criteria.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("evid=" + evid + "&&eventname=" + evName);
}
// DISPLAY TABLE SCORING
function loadScoring() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#eventScoringTableContent tbody").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "get_scoring.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}


// DISPLAYING EVENT MODAL
function openModal() { // Open modal for adding event
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}
// DISPLAYING CONTESTANT MODAL
function openContestantModal() {
    var modal = document.getElementById("contModal");
    modal.style.display = "block";
}
// DISPLAYING COMMITTEE MODAL
function openCommitteeModal() {
    var modal = document.getElementById("comtSpModal");
    modal.style.display = "block";
}
// DISPLAYING JUDGE MODAL
function openJudgesModal() {
    var modal = document.getElementById("judgesModal");
    modal.style.display = "block";
}
// DISPLAYING CRITERIA MODAL
function openCriteriaModal() {
    var modal = document.getElementById("criteriaModal");
    modal.style.display = "block";
}
// DISPLAYING SCORING TABLE MODAL
function openScoringTable() {
    loadScoring(); // Loads the event scoring data

    var modal = document.getElementById("eventScoringTable");
    modal.style.display = "block";
}
// DISPLAYING SCORING MODAL
function openScoreModal() {
    var modal = document.getElementById("scoringModal");
    modal.style.display = "block";
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
    
    fetch(`../admin/EventTeamProcess.php?editID=` + eventID)
    .then(response => response.json())
    .then(data => {
        // Get only checkboxes inside the Edit Modal
        var modal = document.getElementById('editEventModal');
        var checkbox = modal.querySelector('input[name="eventBracket"]');

        // Check or not the checkbox based on the data
        if (data.course) {  // Check if elimination exists
            checkbox.checked = true;
        } else {
            checkbox.checked = false; // Uncheck if elimination not present
        }
    })
    .catch(error => console.error('Error fetching course data:', error));

    var modal = document.getElementById("editEventModal");
    modal.style.display = "block";
}
// Open edit criteria modal
function openEditCriModal(element) {
    var card = element.closest('.edit-icon-cri');
    var criId = card.getAttribute('data-id');
    var criteria = card.getAttribute('data-criteria');
    var pts = card.getAttribute('data-pts');
    var evid = card.getAttribute('data-event-id');
    
    document.getElementById('eventIdCri').value = evid;
    document.getElementById('editcriId').value = criId;
    document.getElementById('editcriteria').value = criteria;
    document.getElementById('editcriPts').value = pts;

    var modal = document.getElementById("editcriteriaModal");
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


// Retrieving the selected text in the adding contestant dropdown
function getSelectedText() {
    var selectElement = document.getElementById('eventId');
    var selectedText = selectElement.options[selectElement.selectedIndex].text; // Get selected text
    
    // Set the selected text to the hidden input field
    document.getElementById('selectedEventText').value = selectedText;
}
function updateNameField() {
    var dropdown = document.getElementById("contestantId");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("contestantName").value = selectedText;
}
function updateComtEvent() {
    var dropdown = document.getElementById("eventIdComt");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("comtEVName").value = selectedText;
}
function updateComtName() {
    var dropdown = document.getElementById("comtId");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("comtName").value = selectedText;
}
function updateJudgeEvField() {
    var dropdown = document.getElementById("eventIdJ");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("judgeEVName").value = selectedText;
}
function updateJudgeField() {
    var dropdownn = document.getElementById("judgeId");
    var selectedText = dropdownn.options[dropdownn.selectedIndex].text;
    document.getElementById("judgeName").value = selectedText;
}
function updateCriField() {
    var dropdown = document.getElementById("eventIdC");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("eventname").value = selectedText;
}
function updateCriteriaField() {
    var dropdown = document.getElementById("eventIdCri");
    var selectedText = dropdown.options[dropdown.selectedIndex].text;
    document.getElementById("editeventname").value = selectedText;
}


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("openModal").addEventListener("click", function () {
        openModal();
    });

    // Form submission for adding event
    document.querySelector('.save-btn-event').addEventListener('click', function(event) {
        event.preventDefault();  // Prevent default form submission

        const eventType = document.getElementById('eventType').value;
        const eventName = document.getElementById('eventName').value;
        const eventCategory = document.getElementById('eventCategory').value;

        if (eventType == '' || eventName == '' || eventCategory == '') {
            Swal.fire({
                title: 'Oops!',
                text: 'All fields are required to be filled in.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        } else { // Proceed inserting the event
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
                console.log('An error occurred: ' + error.message);
            });
        }
    });


        // Form submission for editing event
        document.querySelector('.save-btn-editev').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            const eventType = document.getElementById('eventType').value;
            const eventName = document.getElementById('editeventName').value;
            const eventCategory = document.getElementById('eventCategory').value;
    
            if (eventType == '' || eventName == '' || eventCategory == '') {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All fields are required to be filled in.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else { // Proceed updating event
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
                    console.log('An error occurred: ' + error.message);
                });
            }
        });


        // Form submission for adding contestant
        document.querySelector('.save-btn-contestant').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            getSelectedText();
            updateNameField();

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


        // Form submission for adding committee
        document.querySelector('.save-btn-comt').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            updateComtEvent();
            updateComtName();

            var formData = new FormData(document.querySelector('#addCommitteeForm'));

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

            updateJudgeEvField();
            updateJudgeField();

            var formData = new FormData(document.querySelector('#addJudgesForm'));

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
                console.log('An error occurred: ' + error.message);
            });
        });


        // Form submission for adding criteria
        document.querySelector('.save-btn-cri').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            const eventId = document.getElementById('eventIdCri').value;
            const criteria = document.getElementById('criteria').value;
            const criPts = document.getElementById('criPts').value;

            if (eventId == '' || criteria == '' || criPts == '') {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All fields are required to be filled in.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else { // Proceed inserting criteria
                updateCriField();
                
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
                    console.log('An error occurred: ' + error.message);
                });
            }
        });


        // Form submission for editing criteria
        document.querySelector('.save-btn-editcri').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            const eventId = document.getElementById('eventIdCri').value;
            const criteria = document.getElementById('editcriteria').value;
            const criPts = document.getElementById('editcriPts').value;

            if (eventId == '' || criteria == '' || criPts == '') {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All fields are required to be filled in.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else { // Proceed updating criteria
                updateCriteriaField();

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
            }
        });


        // Form submission for adding score
        document.querySelector('.save-btn-scr').addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default form submission

            const ranknum = document.getElementById('rankNo').value;
            const rankname = document.getElementById('rankName').value;
            const catg = document.getElementById('scoringCategory').value;
            const pts = document.getElementById('scorePts').value;

            if (ranknum == '' || rankname == '' || catg == '' || pts == '') {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All fields are required to be filled in.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
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
            }
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


    //deleting contestant
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon')) {
            var conid = event.target.getAttribute('data-cont');
            var name = event.target.getAttribute('data-event-name');
            var cname = event.target.getAttribute('data-name');

            deleteCont(conid, name, cname);
        }
    });

    //deleting Committee
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-faci')) {
            var id = event.target.getAttribute('data-id');
            var eventname = event.target.getAttribute('data-event-name');
            var idname = event.target.getAttribute('data-name');

            deleteComt(id, eventname, idname);
        }
    });

    //deleting judge
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-judge')) {
            var id = event.target.getAttribute('data-id');
            var name = event.target.getAttribute('data-name');
            var eventn = event.target.getAttribute('data-event-name');

            deleteJudge(id, name, eventn);
        }
    });

    //deleting criteria
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-cri')) {
            var id = event.target.getAttribute('data-id');
            var name = event.target.getAttribute('data-name');
            var eventn = event.target.getAttribute('data-event-name');

            deleteCri(id, name, eventn);
        }
    });

    //deleting score
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-icon-pts')) {
            var name = event.target.getAttribute('data-rank');
            var rname = event.target.getAttribute('data-rank-name');

            deleteScoring(name, rname);
        }
    });
});


// Deletes the contestant
function deleteCont(id, name, contn) {
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
                    contid: id,
                    eventname: name,
                    contname: contn
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
//deleting Committee
function deleteComt(id, event, name) {
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
                    comtid: id,
                    eventname: event,
                    name: name
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
//deleting judge
function deleteJudge(id, name, event) {
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
                    judgeid: id,
                    name: name,
                    eventname: event
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
//deleting criteria
function deleteCri(id, name, event) {
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
                    criid: id,
                    name: name,
                    eventname: event
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
//deleting scoring
function deleteScoring(name, rname) {
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
                    rank: name,
                    rankname: rname
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


// Closes a modal
function closeModal(thisModal) {
    var modal = document.getElementById(thisModal);
    modal.style.display = "none";
}

// Validates a text only input
function validateInput(event) {
    // Allow only letters (both lowercase and uppercase) and spaces
    const regex = /^[a-zA-Z\s]*$/;
    const inputField = event.target;

    if (!regex.test(inputField.value)) {
        inputField.value = inputField.value.replace(/[^a-zA-Z\s]/g, '');
    }
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