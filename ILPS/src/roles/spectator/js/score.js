// Retrieve and display table score for each event
function updateTable() {
    var eventID = document.getElementById('event').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'get_records.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('scoreTable').innerHTML = this.responseText;
        }
    };
    xhr.send('eventID=' + eventID);
}