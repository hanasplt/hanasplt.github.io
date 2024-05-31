<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chestechshopdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$deleted = false;
$error_message = "";

// Get the prod_id from the query string
if (isset($_GET['prod_id']) && isset($_GET['confirm'])) {
    $prod_id = $_GET['prod_id'];

    // Delete the product from the database
    $sql = "DELETE FROM inventory WHERE prod_id = $prod_id";

    if ($conn->query($sql) === TRUE) {
        $deleted = true;
    } else {
        $deleted = false;
        $error_message = "Error deleting product: " . $conn->error;
    }
} elseif (!isset($_GET['confirm'])) {
    $error_message = "Deletion not confirmed.";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['prod_id']) && !isset($_GET['confirm'])): ?>
        // Show the confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the same page with confirm parameter
                window.location.href = 'inventory-delete.php?prod_id=<?php echo $_GET['prod_id']; ?>&confirm=true';
            } else {
                // Redirect back to the inventory page if not confirmed
                window.location.href = 'inventory-delete.php';
            }
        });
        <?php elseif ($deleted): ?>
        Swal.fire({
            title: 'Deleted!',
            text: 'Product deleted successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'inventory-land.php';
            }
        });
        <?php else: ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo $error_message; ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'inventory-land.php';
            }
        });
        <?php endif; ?>
    });
</script>
</body>
</html>
