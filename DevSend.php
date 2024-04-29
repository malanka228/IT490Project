#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($argc < 5) {
    exit(1);
}
$sourceFile = $argv[1];
$username = $argv[2];
$destinationIP = $argv[3];
$destinationDir = $argv[4];
try {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
    $request = array();
    $request['type'] = "Transfer Package To Deployment";
    $request['sourceFile'] = $sourceFile;
    $request['username'] = $username;
    $request['destinationIP'] = $destinationIP;
    $request['destinationDir'] = $destinationDir;
    $response = $client->send_request($request);
    if ($response !== false) {
        echo "Client received response:\n";
        print_r($response);
        echo "\n\n";
    } else {
        echo "Error: Failed to receive response from RabbitMQ.\n";
    }
    echo "Source file: $sourceFile\n";
    echo "Username: $username\n";
    echo "Destination IP: $destinationIP\n";
    echo "Destination directory: $destinationDir\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
