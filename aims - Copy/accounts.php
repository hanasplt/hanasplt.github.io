<?php
    $conn = include 'db.php';

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Pagination setup
    $recordsPerPage = 5;
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $currentPage = max(1, $currentPage);
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Get the search query
    $searchQuery = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%%";  // If no search term, use '%%' (matches everything)

    try {
        // Count accounts
        $countSql = "CALL sp_getAccountCount(?)";
        $countStmt = $conn->prepare($countSql);
        if (!$countStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $countStmt->bind_param("s", $searchQuery);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        if (!$countResult) {
            throw new Exception("Error retrieving account count: " . $countStmt->error);
        }

        $totalAccounts = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalAccounts / $recordsPerPage);

        $countResult->free_result();
        $conn->next_result();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }

    try {
        // Fetch accounts
        $sql = "CALL sp_getAccount(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssi", $searchQuery, $recordsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }

        $result->free_result();
        $stmt->close();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }

    $conn->close();
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
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
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
            ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <p>No accounts found matching your search.</p>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>" class="arrow">&laquo; Previous</a>
        <?php else: ?>
            <a href="#" class="arrow disabled">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>" class="arrow">Next &raquo;</a>
        <?php else: ?>
            <a href="#" class="arrow disabled">Next &raquo;</a>
        <?php endif; ?>
    </div>


    <!-- logout confirmation -->
    <script>
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
                    window.location.href = 'index.html';
                }
            });
        });
    </script>

    
    <script>
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', debounce(function() {
            var searchValue = this.value;
            if (searchValue.length > 0) {
                window.location.href = '?search=' + encodeURIComponent(searchValue) + '&page=1';
            }
        }, 500)); 

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
