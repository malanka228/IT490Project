<?php
/*session_start();

session_unset();
session_destroy();

header("Location: login.html");
exit();*/
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('rabbitmqProducer.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_SESSION['username']) && (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken']))) {
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    $request = array();
    $request['type'] = "Logout";
    $request['username'] = $_SESSION['username'];
    $response = $client->send_request($request);
    echo "client received response: ".PHP_EOL;
    print_r($response);
    echo "\n\n";
    if($response['returnCode'] == 1 && $response['message'] == 'Logged Out'){
    	session_unset();
    	session_destroy();
        header("Location: index.html");
        exit();
    }else if ($response['returnCode'] == 0 && $response['message'] == 'Not Logged In') {
        header("Location: login.html");
        exit();
    }
}else{
    header("Location: login.html");
    exit();
}
?>