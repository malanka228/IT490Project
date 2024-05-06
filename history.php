<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require 'vendor/autoload.php';
if (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])){
    $username = $_SESSION['username'];
    echo "<title>History</title>";
    echo "<link rel='stylesheet' href='style.css'>";
    echo "<div class='topnav'>";
    echo "<a href='landing.php'>Profile</a>";
    echo "<a href='logout.php'>Logout</a>";
    echo "<a href='browse.php'>Browse Product</a>";
    echo "<div class='topnav-right'>";
    echo "<a href='reviews.php'>Reviews</a>";
    echo "<a href='wishlist.php'>Wishlist</a>";
    echo "<a href='history.php'>History</a>";
    echo "</div>";
    echo "</div>";
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = "FetchHistory"; 
    $request['username'] = $username;
    $response = $client->send_request($request);
    if ($response['returnCode'] == 1) { 
        echo "<h1>All History</h1>";
        echo "<div class='reviews'>";
        foreach ($response['history'] as $history) {
            echo "<div class='review'>";
            echo "<p><strong>History:</strong> " . $history['query'] . "</p>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Error retrieving history.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
