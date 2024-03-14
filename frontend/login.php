<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
if (isset($_POST['login']) && (!isset($_SESSION['jwtToken']) && empty($_SESSION['jwtToken']))){
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    $username = $_POST['username'];
    $password = $_POST['password'];
    $request = array();
    $request['type'] = "Login";
    $request['username'] = $username;
    $request['password'] = $password;
    $response = $client-> send_request($request);
    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
    if($response['returnCode'] == 1 && $response['message'] == 'Login Successful'){
        $jwt = isset($response['jwt']) ? $response['jwt'] : null;
        if($jwt){
            session_start();
            $_SESSION['jwtToken'] = $jwt;
            $_SESSION['username'] = $username;
            header("Location: landing.php");
            exit();
        }else{
            echo "JWT token not found";
        }
        exit();
    }else if ($response['returnCode'] == 0 && $response['message'] == 'Invalid login/password') {
        header("Location: login.html?message=InvalidCredentials");
        exit();
    }
}else{
    header("Location: landing.php");
    exit();
}
?>
