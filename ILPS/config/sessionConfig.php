<?php
session_set_cookie_params([
    'lifetime' => 0, // Cookie lasts until the browser is closed
    'path' => '/',
    'domain' => 'localhost', // Specify domain if necessary
    'secure' => true, // Only send cookie over HTTPS
    'httponly' => true, // JavaScript can't access cookie
    'samesite' => 'Strict' // Prevents cookies from being sent with cross-site requests
]);

session_start();

?>