<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('rabbitmqProducer.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['register']) && (!isset($_SESSION['jwtToken']) && empty($_SESSION['jwtToken']))) {
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($password, PASSWORD_DEFAULT);
    $request = array();
    $request['type'] = "Register";
    $request['username'] = $username;
    $request['firstName'] = $firstName;
    $request['lastName'] = $lastName;
    $request['password'] = $password;
    $request['phone'] = $phone;
    $request['email'] = $email;
    $response = $client->send_request($request);
    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
    if($response['returnCode'] == 1 && $response['message'] == 'Account is registered and connected'){
        header("Location: index.html");
        exit();
    }else if ($response['returnCode'] == 0 && $response['message'] == 'Username already exists') {
        header("Location: register.html?message=UsernameAlreadyExists");
        exit();
    }
}else{
    header("Location: landing.php");
    exit();
}
?>
