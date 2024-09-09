<?php
    $conn = include 'db.php';

    $sql = "CALL sp_getAllAcc()"; // retrieving all accounts
    $result = $conn->query($sql);

    $accounts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
    $result->free_result();
    $conn->next_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Accounts</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/accounts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/icons/logo.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';"><b>Accounts</b></p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';">Events</p>
            <p onclick="window.location.href = '#';">Schedule</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" onclick="window.location.href = 'index.html';"></i>
        </div>
    </div>

    <div class="new-account" id="openPopup">
        <div class="plus-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div class="new-account-info">
            <p id="create">Create a New Account</p>
            <p id="add">Add an account for committee/judge.</p>
        </div>
        <div class="iframe-overlay" id="iframeOverlay">
            <iframe id="popupFrame"></iframe>
        </div>
        <script>
            document.getElementById("openPopup").addEventListener("click", function() { // opens the form
                document.getElementById("popupFrame").src = "create-account.html";
                document.getElementById("iframeOverlay").style.display = "block";
            });
            window.addEventListener("message", function(event) {
                if (event.data === "closePopup") {
                    document.getElementById("iframeOverlay").style.display = "none";
                }
            });
        </script>
    </div>

    <div class="accounts">
        <div class="accounts-title">
            <p id="accs">Accounts</p>
            <input type="text" id="searchBox" name="SearchBox" placeholder="Search Account...">
            <div class="dropdowns">
                <div class="sort-by">
                    <select id="sort-type" name="sort-type">
                        <option value="all">All</option>
                        <option value="Committee">Committee</option>
                        <option value="Judge">Judge</option>
                    </select>
                </div>
                <div class="a-z">
                    <select id="abc" name="abc">
                        <option value="" hidden></option>
                        <option value="a-z">A-Z</option>
                        <option value="z-a">Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <?php
        if (!empty($accounts)) {
            foreach ($accounts as $row) {
                $fullName = $row['firstName'];
                if (!empty($row['middleName'])) {
                    $fullName .= " " . $row['middleName'];
                }
                $fullName .= " " . $row['lastName'];
                if (!empty($row['suffix'])) {
                    $fullName .= " " . $row['suffix'];
                }
                ?>
                <div class="account" data-name="<?php echo strtolower($fullName); ?>">
                    <div class="left-deets">
                        <div class="acc-img">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="acc-deets">
                            <p id="name">
                                <?php
                                echo $row['firstName'];
                                if (!empty($row['middleName'])) {
                                    echo " " . $row['middleName'];
                                }
                                echo " " . $row['lastName'];
                                if (!empty($row['suffix'])) {
                                    echo " " . $row['suffix'];
                                }
                                ?>
                            </p>
                            <p><?php echo $row['type']; ?></p>
                        </div>
                    </div>
                    <div class="acc-buttons">
                        <form action="delete-account.php" method="POST" id="deleteForm_<?php echo $row['userId']; ?>">
                            <input type="hidden" name="userId" value="<?php echo $row['userId']; ?>">
                            <button type="button" class="trash-icon" style="cursor: pointer;" onclick="confirmDelete('<?php echo $row['userId']; ?>')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                        <div class="edit-icon" data-user-id="<?php echo $row['userId']; ?>">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div class="popup" id="popupEdit">
                            <iframe id="editIframe" src="edit-account.html"></iframe>
                        </div>
                        <script>
                            document.querySelectorAll('.edit-icon').forEach(function(editIcon) {
                                editIcon.addEventListener('click', function() {
                                    var userId = this.getAttribute('data-user-id');
                                    var iframe = document.getElementById('editIframe');
                                    iframe.src = 'edit-account.html?userId=' + userId;
                                    document.querySelector('.popup').style.display = 'block';
                                });
                            });
                            window.addEventListener("message", function(event) {
                                if (event.data === "closePopup") {
                                    document.getElementById("popupEdit").style.display = "none";
                                }
                            });
                        </script>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "0 results"; // no users were added yet.
        }
        $conn->close();
        ?>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', function() {
            var searchValue = this.value.toLowerCase();
            var accounts = document.querySelectorAll('.account');

            accounts.forEach(function(account) {
                var name = account.getAttribute('data-name');
                if (name.includes(searchValue)) {
                    account.style.display = 'flex';
                } else {
                    account.style.display = 'none';
                }
            });
        });

        // Edit functionality
        document.querySelectorAll('.edit-icon').forEach(function(editIcon) {
            editIcon.addEventListener('click', function() {
                var userId = this.getAttribute('data-user-id');
                var iframe = document.getElementById('editIframe');
                iframe.src = 'edit-account.html?userId=' + userId;
                document.getElementById('popupEdit').style.display = 'block';
            });
        });

        window.addEventListener("message", function(event) {
            if (event.data === "closePopup") {
                document.getElementById("popupEdit").style.display = "none";
            }
        });

        function confirmDelete(userId) {
            if (confirm("Are you sure you want to delete this account?")) {
                document.getElementById('deleteForm_' + userId).submit();
            }
        }
    </script>

    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Confirm',
                text: "Do you want to delete this account?",
                icon: 'warning',
                cancelButtonColor: '#8F8B8B',
                confirmButtonColor: '#7FD278',
                confirmButtonText: 'Confirm',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete-account.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            userId: userId
                        })
                    }).then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Network response was not ok.');
                    }).then(data => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Account deleted successfully.',
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
                            text: 'Error deleting account.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }

        document.getElementById('sort-type').addEventListener('change', function() { // sort by account type
            var selectedType = this.value;
            var accounts = document.querySelectorAll('.account');

            accounts.forEach(function(account) {
                var type = account.querySelector('.acc-deets p:last-child').textContent.trim();

                if (selectedType === 'all' || type === selectedType) {
                    account.style.display = 'flex';
                } else {
                    account.style.display = 'none';
                }
            });
        });

        document.getElementById('abc').addEventListener('change', function() { // sort alphabetically
            var sortOrder = this.value;
            var accountsContainer = document.querySelector('.accounts');
            var accounts = Array.from(accountsContainer.querySelectorAll('.account'));

            if (sortOrder === 'a-z') {
                accounts.sort(function(a, b) {
                    var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
                    var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
                    if (nameB < nameA) return 1;
                    if (nameB > nameA) return -1;
                    return 0;
                });
            } else if(sortOrder === 'z-a') {
                accounts.sort(function(a, b) {
                    var nameA = a.querySelector('#name').textContent.trim().toUpperCase();
                    var nameB = b.querySelector('#name').textContent.trim().toUpperCase();
                    if (nameB < nameA) return -1;
                    if (nameA > nameB) return 1;
                    return 0;
                });
            }

            accounts.forEach(function(account) {
                accountsContainer.appendChild(account);
            });
        });
    </script>
</body>
</html>
