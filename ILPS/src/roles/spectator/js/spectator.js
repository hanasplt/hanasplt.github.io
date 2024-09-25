// Retrieve and display table on overall score
function updateTable() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'overall.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('scoreTable').innerHTML = this.responseText;
        }
    };
    xhr.send();
}