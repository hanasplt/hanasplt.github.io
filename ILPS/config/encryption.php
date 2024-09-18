<?php
function encrypt($plaintext, $key) {
    $cipher = array_combine(array_merge(range('a', 'z'), range('0', '9')), str_split($key));
    $encrypted = '';
    
    foreach (str_split($plaintext) as $char) {
        if (ctype_alnum($char)) {
            $char = strtolower($char);
            $encrypted .= isset($cipher[$char]) ? $cipher[$char] : $char;
        } else {
            $encrypted .= $char;
        }
    }
    
    return $encrypted;
}

function decrypt($ciphertext, $key) {
    $cipher = array_combine(str_split($key), array_merge(range('a', 'z'), range('0', '9')));
    $decrypted = '';
    
    foreach (str_split($ciphertext) as $char) {
        if (ctype_alnum($char)) {
            $char = strtolower($char);
            $decrypted .= isset($cipher[$char]) ? $cipher[$char] : $char;
        } else {
            $decrypted .= $char;
        }
    }
    
    return $decrypted;
}

$encryption_key = "zyxwvutsrqponmlkjihgfedcba0123456789";

function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$gameid = generateRandomString(5);
?>