<?php
define("ENCRYPT_METHOD", "AES-256-CBC");
define("SECRET_KEY", "g4D&*s8Yz#1lF9qpV!72wKj34PU8?)_"); 
define("SECRET_IV", "n7$3LrX!2pQvWs8K");    

function encryptPassword($password) {
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_encrypt($password, ENCRYPT_METHOD, $key, 0, $iv);
}

function decryptPassword($encryptedPassword) {
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_decrypt($encryptedPassword, ENCRYPT_METHOD, $key, 0, $iv);
}
?>
