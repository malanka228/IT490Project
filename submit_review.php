<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require 'vendor/autoload.php';
if (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $itemTitle = $_POST['itemTitle'];
        $reviewText = $_POST['review'];
        $username = $_SESSION['username'];
        $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
        $request = array();
        $request['type'] = "SubmitReview";
        $request['username'] = $username;
        $request['itemTitle'] = $itemTitle;
        $request['reviewText'] = $reviewText;
        $response = $client->send_request($request);
        if ($response['returnCode'] == 1 && $response['message'] == 'Review submitted successfully') {
            echo "Review submitted successfully.";
        } else {
            echo "Error submitting review.";
        }
    } else {
        echo "Invalid request method.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
