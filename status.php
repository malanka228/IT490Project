#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($argc < 4) {
    echo "Usage: $argv[0] <sourceFile> <versionNumber> <statusCode>\n";
    exit(1);
}
$sourceFile = $argv[1];
$versionNumber = $argv[2];
$status = $argv[3];
try {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
    $request = array();
    $request['type'] = "Update Status Code";
    $request['sourceFile'] = $sourceFile;
    $request['versionNumber'] = $versionNumber;
    $request['status'] = $status;
    $response = $client->send_request($request);
    if ($response !== false) {
        echo "Client received response:\n";
        print_r($response);
        echo "\n\n";
    } else {
        echo "Error: Failed to receive response from RabbitMQ.\n";
    }
    echo "Source file: $sourceFile\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
