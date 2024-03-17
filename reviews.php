<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require 'vendor/autoload.php';
if (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])) {
    echo "<link rel='stylesheet' href='style.css'>";
    echo "<div class='topnav'>";
    echo "<a href='landing.php'>Profile</a>";
    echo "<a href='logout.php'>Logout</a>";
    echo "<a href='browse.php'>Browse Product</a>";
    echo "<div class='topnav-right'>";
    echo "<a href='notifications.php'>Notifications</a>";
    echo "<a href='reviews.php'>Reviews</a>";
    echo "<a href='wishlist.php'>Wishlist</a>";
    echo "</div>";
    echo "</div>";
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = "FetchReviews"; 
    $response = $client->send_request($request);
    if ($response['returnCode'] == 1) { 
        echo "<h1>All Reviews</h1>";
        echo "<div class='reviews'>";
        foreach ($response['reviews'] as $review) {
            echo "<div class='review'>";
            echo "<p><strong>User:</strong> " . $review['Username'] . "</p>";
            echo "<p><strong>Item:</strong> " . $review['ItemTitle'] . "</p>";
            echo "<p><strong>Review:</strong> " . $review['ReviewText'] . "</p>";
            echo "<p class='timestamp'><strong>Timestamp:</strong> " . $review['Timestamp'] . "</p>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Error retrieving reviews.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
