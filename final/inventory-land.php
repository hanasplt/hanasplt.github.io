<!DOCTYPE html>
<html>
<head>
<title>Inventory</title>
 <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="inventory.css">
        <!-- fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
		<!--Web-logo-->
		<link rel="icon" href="icons/logo.svg">
		<!---->
</head>
<body>
<div class="menu">
            <div class="close-button">
                <i id="menuIcon" class="fas fa-times icon-white"></i>
            </div>
            <div class="admin">
                <h1>Welcome,</h1>
                <img src="icons/logo.png"/>
                <p><b>Admin</b></p>
            </div>

            <div class="buttons">
                <input type="button" name="dash" id="dashButton" value="Dashboard">
                <input type="button" name="app" id="appButton" value="Appointments">
                <input type="button" name="reserv" id="reservButton" value="Reservations">
                <input type="button" name="inv" id="invButton" value="Inventory">
                <input type="button" name="history" id="historyButton" value="History">
                <input type="button" name="bill" id="billButton" value="Billing">
            </div>

            <div class="logout">
                <input type="button" name="logout" id="logoutButton" value="LOG OUT">
            </div>
        </div>

        <div class="mainContent">
         <div class="rightSide" id="dash">
                <i id="menuIcon" class="fas fa-bars icon-white"></i>
                <h1>Today's Date:</h1>
                <p id="date">Date Placeholder</p>
            </div>
			 <div class="formContainer">
            <form>
                <select id="dropdown" name="dropdown">
					<option value="All Types" data-href="#">All Types</option>
					<option value="Phone" data-href="inventory_phone.html">Phone</option>
					<option value="Accessories" data-href="inventory_access.html">Accessories</option>
				</select>
            </form>
			</div>
			<div class="content">
			<div class="new-inventory">
                <p id="inventory-title">Inventory</p>
				<a href="inventory-add.html" class="btn-add">Add</a>
				<br />
                <div class="inventory-content">
                    <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "chestechshopdb";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // SQL query to fetch data
                        $sql = "SELECT prod_id, prod_type, prod_brand, prod_model FROM inventory";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo "<div class='inventory-content-item'>";
                                echo "<div class='inventory-label'>" . $row["prod_id"] . " | " . $row["prod_brand"] . " | " . $row["prod_type"] . " | " . $row["prod_model"] . "</div>";
                                echo "<div class='inventory-button'><a href='inventory-details-phone.html' class='btn'>View</a></div>";
                                echo "</div>";
                                echo "<hr class=\"inventory-line\" />";
                            }
                        } else {
                            echo "0 results";
                        }
                        $conn->close();
                    ?>
				</div>
            </div>
        </div>
		<script src="inventory.js"></script>
</body>
</html>
