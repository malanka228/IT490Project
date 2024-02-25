<?php session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
if (isset($_POST['login'])){
    $username = $_POST['username'];
    $userData = [
        'username' => $username
    ];
    $secret = "secret";
    $expiration = time() + 3600;
    $token = JWT::encode(['username' => $username, 'expiration' => $expiration], $secret);
    echo $token;
}

?>
