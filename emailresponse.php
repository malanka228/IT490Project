#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
function sendEmail($emails)
{
  foreach ($emails as $email) {
  $to = "$email";
  $subject = "PricifyInspect Price Alert";
  $message = "This weeks reminder to check the items in your wishlist to see if any prices have dropped.";
  $headers = "From: sender@pricifyinspect.com";
  

  // Send email
  if (mail($to, $subject, $message, $headers)) {
      echo "Email sent successfully!";
  } else {
      echo "Failed to send email.";
  }
    }
}

function requestProcessor($request)
{
    echo "received request".PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    switch ($request['type']) {
        case 'EmailReceiving':
            return sendEmail($request['emails']);
            break;
        
    }
}
$server = new rabbitMQServer("testRabbitMQ.ini", "testPhones");
$server->process_requests('requestProcessor');
exit();
?>