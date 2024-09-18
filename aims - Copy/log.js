// Display Access Log
window.onload = function() {
    loadAccessLog();
};

function loadAccessLog() {
    // Fetch the logs
    var table = document.getElementById("tableLog"); // Get the table id

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.querySelector("#" + table.id + " tbody").innerHTML = this.responseText;
        }
    };
    
    xhttp.open("POST", "get_logs.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}