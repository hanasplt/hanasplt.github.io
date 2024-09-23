<?php
function encrypt($plaintext, $key) {
    $cipher_lower = array_combine(range('a', 'z'), str_split(substr($key, 0, 26)));
    $cipher_upper = array_combine(range('A', 'Z'), str_split(strtoupper(substr($key, 0, 26))));
    $cipher_digits = array_combine(range('0', '9'), str_split(substr($key, 26)));
    
    $encrypted = '';
    
    foreach (str_split($plaintext) as $char) {
        if (ctype_lower($char)) {
            // Encrypt lowercase letters
            $encrypted .= isset($cipher_lower[$char]) ? $cipher_lower[$char] : $char;
        } elseif (ctype_upper($char)) {
            // Encrypt uppercase letters
            $encrypted .= isset($cipher_upper[$char]) ? $cipher_upper[$char] : $char;
        } elseif (ctype_digit($char)) {
            // Encrypt digits
            $encrypted .= isset($cipher_digits[$char]) ? $cipher_digits[$char] : $char;
        } else {
            // Keep other characters as is
            $encrypted .= $char;
        }
    }
    
    return $encrypted;
}

function decrypt($ciphertext, $key) {
    $cipher_lower = array_combine(str_split(substr($key, 0, 26)), range('a', 'z'));
    $cipher_upper = array_combine(str_split(strtoupper(substr($key, 0, 26))), range('A', 'Z'));
    $cipher_digits = array_combine(str_split(substr($key, 26)), range('0', '9'));
    
    $decrypted = '';
    
    foreach (str_split($ciphertext) as $char) {
        if (ctype_lower($char)) {
            // Decrypt lowercase letters
            $decrypted .= isset($cipher_lower[$char]) ? $cipher_lower[$char] : $char;
        } elseif (ctype_upper($char)) {
            // Decrypt uppercase letters
            $decrypted .= isset($cipher_upper[$char]) ? $cipher_upper[$char] : $char;
        } elseif (ctype_digit($char)) {
            // Decrypt digits
            $decrypted .= isset($cipher_digits[$char]) ? $cipher_digits[$char] : $char;
        } else {
            // Keep other characters as is
            $decrypted .= $char;
        }
    }
    
    return $decrypted;
}

$encryption_key = "zyxwvutsrqponmlkjihgfedcba0123456789";
?>
