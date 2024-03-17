<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
function sendToRabbitMQ($type, $username, $firstName, $lastName, $browserSession, $lastLogin){
	$credentials = [
		'host' => '10.144.132.77',
		'port' => 5672,
		'user' => 'Alex',
		'password' => '1220',
		'vhost' => 'testHost',
		];
	$connection = new AMQPStreamConnection(
		$credentials['host'],
        $credentials['port'],
        $credentials['user'],
        $credentials['password'],
        $credentials['vhost']

	);
	$channel = $connection->channel();

    // Declare a request queue named 'registration_request_queue'
    $channel->queue_declare('registration_request_queue', false, true, false, false);

    // Declare a response queue named 'registration_response_queue'
    $channel->queue_declare('registration_response_queue', false, true, false, false);

    // Prepare the message body
    $messageBody = json_encode(['type' => $type, 'username' => $username, 'firstName' => $firstName, 'lastName' => $lastName, 'browserSession' => $browserSession, 'lastLogin' => $lastLogin, 'response_queue' => 'registration_response_queue']);

    // Create a new AMQPMessage
    $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

    // Publish the message to the 'registration_request_queue'
    $channel->basic_publish($message, '', 'registration_request_queue');

    // Close the channel and connection
    $channel->close();
    $connection->close();
}
?>