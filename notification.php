#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('rabbitmqProducer.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request = array();
$request['type'] = "Notification";
$response = $client->send_request($request);
echo "client received response: ".PHP_EOL;
echo "\n\n";
if($response['returnCode'] == 1){
    $phoneNumbers = $response['phoneNumbers'];
    $emails = $response['emails'];
    foreach ($phoneNumbers as $phoneNumber) {

        $ch = curl_init('https://textbelt.com/text');
        $data = array(
            'phone' => $phoneNumber,
            'message' => 'Hi, it\'s PricifyInspect! ' . PHP_EOL .'This weeks reminder to check the items in your wishlist to see if any prices have dropped.',
            'key' => '8744a12a45acd1f135117147582f54d9cd99923anzLl36JKrCnL2njBmO7MRezTI',
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
    }
    $client = new rabbitMQClient("testRabbitMQ.ini","testPhones");
    $request = array();
    $request['type'] = "EmailReceiving";
    $request['emails'] = $emails; 
    $response = $client->send_request($request);
    echo "client received response: ".PHP_EOL;
    echo "\n\n";
}
?>
