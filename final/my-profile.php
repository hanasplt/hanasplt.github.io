<?php
include "db-connection.php";

session_start();
$error = "";

if(!isset($_SESSION['username'])) {
 header("Location: index.html");
 exit(); 
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM client_accounts WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
 $row = $result->fetch_assoc();
 $first_name = htmlspecialchars($row['first_name']);
 $last_name = htmlspecialchars($row['last_name']);
 $phone_number = htmlspecialchars($row['phone_number']);
 $email = htmlspecialchars($row['email']);
 $client_img = htmlspecialchars($row['profile_picture']);
} else {
    echo "User not found!";
    exit();
}

$success = false;
if(isset($_POST['delete'])) {
    $sql = "DELETE FROM client_accounts WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $success = true;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>CHES Cellphone and Accessories Shop</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--css-->
  <link href="my-profile.css" type="text/css" rel="stylesheet" />
  <!---->

  <!--fonts-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
  <!---->

  <!--icons-->
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-straight/css/uicons-regular-straight.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>
  <!---->

  <!--Web-logo-->
  <link rel="icon" href="icons/logo.svg">
  <!---->

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!---->
</head>
<body>
    <!-- Your HTML content with PHP variables to display user information -->
    <div class="header">
      <div class="left-header">
      <i class="fi fi-rr-arrow-small-left" id="back"></i>
      <p>My Profile</p>
     </div>
     <div class="right-header">
      <img src="icons/logo.svg">
      <p>CHES</p>
    </div>
    </div>
    <form method="post">
        <div class="main-container">
            <!-- Display user information -->
            <div class="left-main">
                <p>First Name</p>
                <div class="display-info"><span><?php echo $first_name; ?></span></div><br>
                <p>Last Name</p>
                <div class="display-info"><span><?php echo $last_name; ?></span></div><br>
                <p>Phone Number</p>
                <div class="display-info"><span>0<?php echo $phone_number; ?></span></div><br>
                <p>Email</p>
                <div class="display-info"><span><?php echo $email; ?></span></div><br>
            </div>
            <div class="vertical"></div>
            <!-- Right side content -->
            <div class="right-main">
            <?php if (!empty($client_img) && file_exists('images/' . $client_img)) : ?>
                    <img id="uploadedImageID" src="images/<?php echo $client_img ; ?>" class="pfp" alt="User Image" name="client_img"><br>
                <?php else: ?>
                    <img id="uploadedImageID" src="icons/profile.png" class="pfp" alt="User Image"><br>
                <?php endif; ?>
                <button type="button" class="edit-profile" id="editProfileID">EDIT PROFILE</button><br>
                <button type="submit" class="delete-account" id="deleteAccountID" name="delete">DELETE ACCOUNT</button>
            </div>
        </div>
    </form>
    <!-- Include your JavaScript file -->
    <script type="text/javascript">
        let editProfileButton = document.querySelector('#editProfileID');
        let homepage = document.querySelector('#back');

        editProfileButton.onclick = () => {
        window.location.href = "edit-profile.php";
        }

        homepage.onclick = () => {
        window.location.href = "cp-homepage.php";
    }
    </script>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $success): ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F79256',
        cancelButtonColor: '#2A5181',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Logging out..',
                text: 'Your profile has been deleted successfully.',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                <?php session_destroy();?>
                window.location.href = 'index.html';
            });
        }
    });
});
</script>
<?php endif; ?>


</body>
</html>
