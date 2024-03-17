<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('rabbitmqProducer.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['searchProduct']) && (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken']))) {
    $client = new rabbitMQClient("testRabbitMQ.ini","testDMZ");
    $productName = $_POST['product_name'];
    $request = array();
    $request['type'] = "searchProduct";
    $request['productName'] = $productName;
    $response = $client->send_request($request);
    echo "client received response: ".PHP_EOL;
    echo "\n\n";
    if($response['returnCode'] == 1 && $response['message'] == 'Product Found'){
        if(isset($response['responseData'])){
            $_SESSION['responseData'] = $response['responseData'];
            header("Location: ebay.php");
            exit();
        }
    }else if ($response['returnCode'] == 0 && $response['message'] == "Error decoding JSON response or 'results' key not found") {
        header("Location: browse.php");
        exit();
    }
}else{
    header("Location: login.php");
    exit();
}
?>
