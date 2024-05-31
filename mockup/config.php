<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chestechshopdb";

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$check_db = "SHOW DATABASES LIKE '$dbname'";
$check_result = mysqli_query($conn, $check_db);

if (mysqli_num_rows($check_result) > 0) {
    echo "Database already exists<br>";
} else {
    $sql = "CREATE DATABASE $dbname";

    if (mysqli_query($conn, $sql)) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . mysqli_error($conn);
    }
}

mysqli_close($conn);

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$tables = array("client_accounts", "inventory", "product_colors", "appointments", "reservations", "services", "purchases", "claim");

foreach ($tables as $table) {
    $check_table = "SHOW TABLES LIKE '$table'";
    $check_result = mysqli_query($conn, $check_table);

    if (mysqli_num_rows($check_result) > 0) {
        echo "Table $table already exists<br>";
    } else {
        switch ($table) {
            case "client_accounts":
                $tbAcc = "CREATE TABLE client_accounts (
                    client_id int NOT NULL AUTO_INCREMENT,
                    first_name varchar(50) NOT NULL,
                    last_name varchar(50) NOT NULL,
                    username varchar(15) NOT NULL,
                    phone_number bigint(11) NOT NULL,
                    email varchar(50),
                    pass varchar(50) NOT NULL,
                    profile_picture varchar(60) NOT NULL,
                    PRIMARY KEY (client_id)
                )";

                if (mysqli_query($conn, $tbAcc)) {
                    echo "Table Client Accounts created successfully<br>";
                } else {
                    echo "Error creating Client Accounts table: " . mysqli_error($conn);
                }
                break;

            case "inventory":
                $tbCpInv = "CREATE TABLE inventory (
                    prod_id int NOT NULL AUTO_INCREMENT,
                    prod_type varchar(50) NOT NULL,
                    prod_brand varchar(50) NOT NULL,
                    prod_model varchar(50) NOT NULL,
                    prod_color varchar(50) NOT NULL,
                    prod_quantity int NOT NULL,
                    prod_unit_price double NOT NULL,
                    prod_specs varchar(255) NOT NULL,
                    prod_images longblob,
                    PRIMARY KEY (prod_id),
                    UNIQUE KEY (prod_brand, prod_model, prod_unit_price, prod_color )
                )";

                if (mysqli_query($conn, $tbCpInv)) {
                    echo "Table inv created successfully<br>";
                } else {
                    echo "Error creating inv table: " . mysqli_error($conn);
                }
                break;

            case "appointments":
                $tbAppt = "CREATE TABLE appointments (
                    appt_id int NOT NULL AUTO_INCREMENT,
                    appt_first_name varchar(50) NOT NULL,
                    appt_last_name varchar(50) NOT NULL,
                    appt_phone_number bigint(11) NOT NULL,
                    appt_email varchar(50),
                    appt_brand_model varchar(50) NOT NULL,
                    appt_unit_issue varchar(255) NOT NULL,
                    appt_date date NOT NULL,
                    appt_time time NOT NULL,
                    PRIMARY KEY (appt_id),
                    UNIQUE KEY (appt_id, appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue)
                )";

                if (mysqli_query($conn, $tbAppt)) {
                    echo "Table appts created successfully<br>";
                } else {
                    echo "Error creating appts table: " . mysqli_error($conn);
                }
                break;

            case "reservations":
                $tbRes = "CREATE TABLE reservations (
                    reserve_id int NOT NULL AUTO_INCREMENT,
                    prod_brand varchar(50) NOT NULL,
                    prod_model varchar(50) NOT NULL,
                    prod_unit_price double NOT NULL,
                    prod_color varchar(50) NOT NULL,
                    reserve_first_name varchar(50) NOT NULL,
                    reserve_last_name varchar(50) NOT NULL,
                    reserve_phone_number bigint(11) NOT NULL,
                    reserve_email varchar(50),
                    reserve_quantity int NOT NULL,
                    reserve_total double NOT NULL,
                    reserve_dateToPurchase date NOT NULL,
                    PRIMARY KEY (reserve_id),
                    UNIQUE KEY (reserve_id, prod_brand, prod_model, prod_unit_price, prod_color, 
                                reserve_first_name, reserve_last_name, reserve_phone_number, 
                                reserve_email, reserve_quantity, reserve_total)
                )";

                if (mysqli_query($conn, $tbRes)) {
                    echo "Table Reservations created successfully<br>";
                } else {
                    echo "Error creating Reservations table: " . mysqli_error($conn);
                }
                break;

            case "services":
                $tbServ = "CREATE TABLE services (
                    service_id int NOT NULL AUTO_INCREMENT,
                    appt_id int NOT NULL,
                    appt_first_name varchar(50) NOT NULL,
                    appt_last_name varchar(50) NOT NULL,
                    appt_phone_number bigint                     (11) NOT NULL,
                    appt_email varchar(50),
                    appt_brand_model varchar(50) NOT NULL,
                    appt_unit_issue varchar(255) NOT NULL,
                    services_rendered varchar(255) NOT NULL,
                    services_fee double NOT NULL,
                    services_bill_date date NOT NULL,
                    PRIMARY KEY (service_id),
                    FOREIGN KEY (appt_id, appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue)
                        REFERENCES appointments(appt_id, appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue)
                )";

                if (mysqli_query($conn, $tbServ)) {
                    echo "Table services created successfully<br>";
                } else {
                    echo "Error creating services table: " . mysqli_error($conn);
                }
                break;

            case "purchases":
                $tbPur = "CREATE TABLE purchases (
                    purchase_id int NOT NULL AUTO_INCREMENT,
                    purchase_first_name varchar(50) NOT NULL,
                    purchase_last_name varchar(50) NOT NULL,
                    purchase_phone_number bigint(11) NOT NULL,
                    purchase_email varchar(50),
                    prod_id int NOT NULL,
                    prod_type varchar(50) NOT NULL,
                    prod_brand varchar(50) NOT NULL,
                    prod_model varchar(50) NOT NULL,
                    prod_color varchar(50) NOT NULL,
                    prod_quantity int NOT NULL,
                    prod_unit_price double NOT NULL,
                    purchase_total double NOT NULL,
                    purchase_date date NOT NULL,
                    PRIMARY KEY (purchase_id),
                    FOREIGN KEY (prod_id) REFERENCES inventory(prod_id)
                )";

                if (mysqli_query($conn, $tbPur)) {
                    echo "Table purchases created successfully<br>";
                } else {
                    echo "Error creating purchases table: " . mysqli_error($conn);
                }
                break;

            case "claim":
                $tbClaim = "CREATE TABLE claim (
                    claim_id int NOT NULL AUTO_INCREMENT,
                    reserve_id int NOT NULL,
                    prod_brand varchar(50) NOT NULL,
                    prod_model varchar(50) NOT NULL,
                    prod_price double NOT NULL,
                    prod_color varchar(50) NOT NULL,
                    reserve_first_name varchar(50) NOT NULL,
                    reserve_last_name varchar(50) NOT NULL,
                    reserve_phone_number bigint(11) NOT NULL,
                    reserve_email varchar(50),
                    reserve_quantity int NOT NULL,
                    reserve_total double NOT NULL,
                    claim_payment double NOT NULL,
                    claim_date date NOT NULL,
                    PRIMARY KEY (claim_id),
                    FOREIGN KEY (reserve_id) REFERENCES reservations(reserve_id)
                )";

                if (mysqli_query($conn, $tbClaim)) {
                    echo "Table claim created successfully<br>";
                } else {
                    echo "Error creating claim table: " . mysqli_error($conn);
                }
                break;
        }
    }
}

mysqli_close($conn);
?>

