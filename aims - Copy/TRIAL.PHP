<?php 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    echo ucfirst($name);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        name: 
        <input type="text" name="name" id="name" required oninput="validateInput(event)">

        <button>submit</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validateInput(event) {
            // Allow only letters (both lowercase and uppercase) and spaces
            const regex = /^[a-zA-Z\s]*$/;
            const inputField = event.target;

            if (!regex.test(inputField.value)) {
                inputField.value = inputField.value.replace(/[^a-zA-Z\s]/g, '');
            }
        }
    </script>
</body>
</html>