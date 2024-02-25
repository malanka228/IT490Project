<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('rabbitmqProducer.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['register'])) {
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];
    $browserSession = $_POST['browser'];
    $lastLogin = $_POST['lastLogin'];
    $password = password_hash($password, PASSWORD_DEFAULT);
    $request = array();
    $request['type'] = "Register";
    $request['username'] = $username;
    $request['firstName'] = $firstName;
    $request['lastName'] = $lastName;
    $request['password'] = $password;
    $request['browserSession'] = $browserSession;
    $request['lastLogin'] = $lastLogin;
    $response = $client->send_request($request);
    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
    if($response['returnCode'] == 1){
        header("Location: landing.html");
    }else{
        echo "Not allowed!";
    }
}
?>
