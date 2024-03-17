#!/usr/bin/php
<?php
// rabbitmq_consumer.php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function processRegistrationMessage($messageBody) {
    $data = json_decode($messageBody, true);

    $responseQueue = isset($data['response_queue']) ? $data['response_queue'] : 'default_response_queue';
    if (isset($data['username'])) {
        $username = $data['username'];
        // Additional processing if needed

        // Send a response to the response queue
        sendResponse($responseQueue, 'Registration successful! Thank you, ' . $username . ', for registering.');
    } else {
        // Send an error response to the response queue
        sendResponse($responseQueue, 'Error processing registration data.');
    }
}

function sendResponse($responseQueue, $message) {
    $rabbitmqConfig = [
        'host' => '10.144.132.77',
        'port' => 5672,
        'user' => 'Alex',
        'password' => '1220',
        'vhost' => 'testHost',
    ];

    $connection = new AMQPStreamConnection(
        $rabbitmqConfig['host'],
        $rabbitmqConfig['port'],
        $rabbitmqConfig['user'],
        $rabbitmqConfig['password'],
        $rabbitmqConfig['vhost']
    );

    $channel = $connection->channel();

    // Publish the response to the specified response queue
    $responseMessage = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    $channel->basic_publish($responseMessage, '', $responseQueue);

    // Close the channel and connection
    $channel->close();
    $connection->close();
}

$rabbitmqConfig = [
    'host' => '10.144.132.77',
    'port' => 5672,
    'user' => 'Alex',
    'password' => '1220',
    'vhost' => 'testHost',
];

$connection = new AMQPStreamConnection(
    $rabbitmqConfig['host'],
    $rabbitmqConfig['port'],
    $rabbitmqConfig['user'],
    $rabbitmqConfig['password'],
    $rabbitmqConfig['vhost']
);

$channel = $connection->channel();

// Declare a request queue named 'registration_request_queue'
$channel->queue_declare('registration_request_queue', false, true, false, false);

// Callback function to process incoming messages
$callback = function ($message) {
    processRegistrationMessage($message->body);
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
};

// Consume messages from the 'registration_request_queue'
$channel->basic_consume('registration_request_queue', '', false, false, false, false, $callback);

// Keep the consumer running
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close the channel and connection
$channel->close();
$connection->close();
if (strpos($message, 'successful') !== false) {
        header('Location: landing.html');
        exit();  // Make sure to exit after sending the Location header
    }
?>
