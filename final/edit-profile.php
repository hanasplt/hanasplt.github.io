<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "db-connection.php";

$sql = "SELECT * FROM client_accounts WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists and fetch data
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

if (isset($_POST["saveChanges"])) {
    $fname = $_POST["firstname"];
    $lname = $_POST["lastname"];
    $pnumber = $_POST["phonenumber"];
    $email = $_POST["email"];

    $newImageName = "";
    if ($_FILES["profilepic"]["error"] != 4) {
        $fileName = $_FILES["profilepic"]["name"];
        $fileSize = $_FILES["profilepic"]["size"];
        $tmpName = $_FILES["profilepic"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script>alert('Invalid Image Extension');</script>";
        } elseif ($fileSize > 1000000) {
            echo "<script>alert('Image Size Is Too Large');</script>";
        } else {
            $newImageName = uniqid();
            $newImageName .= '.' . $imageExtension;

            move_uploaded_file($tmpName, 'images/' . $newImageName);
        }
    }

    if ($newImageName) {
        $sql = "UPDATE client_accounts SET first_name=?, last_name=?, phone_number=?, email=?, profile_picture=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $fname, $lname, $pnumber, $email, $newImageName, $username);
    } else {
        $sql = "UPDATE client_accounts SET first_name=?, last_name=?, phone_number=?, email=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $fname, $lname, $pnumber, $email, $username);
    }

    if ($stmt->execute()) {
        $success = true;
    } else {
        echo "<script>alert('Error Updating Profile');</script>";
    }
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
  <link href="edit-profile.css" type="text/css" rel="stylesheet" />
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
  <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
  <!---->

  <!--Web-logo-->
  <link rel="icon" href="icons/logo.svg">
  <!---->

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!---->
</head>
<body>

  <!--header-->
  <div class="header">
   <div class="left-header">
    <i class="fi fi-rr-arrow-small-left" id="backButtonID"></i>
    <p>Edit Profile</p>
   </div>
   <div class="right-header">
    <img src="icons/logo.svg">
    <p>CHES</p>
   </div>
  </div>
  <!---->

  <!--main container-->
  <form id="editProfile" method="post" enctype="multipart/form-data">
   <div class="main-container">
    <div class="left-main">
     <img id="profilePic" src="icons/profile.png" <?php if ($client_img !== "") echo 'src="images/' . $client_img . '"'; ?> alt="Upload Image" class="pfp"><br>
     <label for="inputFile">+</label>
     <input type="file" id="inputFile" name="profilepic" accept="image/jpeg, image/png, image/jpg"><br>
     <button type="submit" class="save-changes" id="saveID" name="saveChanges">SAVE CHANGES</button><br>
     <button type="button" class="change-pass" id="changePassButton" name="changePass">CHANGE PASSWORD</button>
    </div>
    <div class="vertical"></div>
    <div class="right-main">
     <p>First Name</p>
     <input type="text" name="firstname" class="display-info" id="firstNameID" value="<?php echo $first_name; ?>"><br>
     <p>Last Name</p>
     <input type="text" name="lastname" class="display-info" id="lastNameID" value="<?php echo $last_name; ?>"><br>
     <p>Phone Number</p>
     <input type="text" name="phonenumber" class="display-info" id="phoneNumberID" value="0<?php echo $phone_number; ?>"><br>
     <p>Email</p>
     <input type="email" name="email" class="display-info" id="emailID" value="<?php echo $email; ?>"><br>
    </div>
   </div>
  </form>

  <script type="text/javascript">
    let backButton = document.querySelector('#backButtonID');
    let passButton = document.querySelector('#changePassButton');
    backButton.onclick = () => {
        window.location.href = "my-profile.php";
    }
    passButton.onclick = () => {
        window.location.href = "change-password.php";
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
            Swal.fire(
                'Updated!',
                'Your profile has been updated successfully.',
                'success'
            ).then(() => {
                window.location.href = 'my-profile.php';
            });
        }
    });
});
</script>
<?php endif; ?>

</body>
</html>


"