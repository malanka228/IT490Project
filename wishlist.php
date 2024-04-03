<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc'); 
require 'vendor/autoload.php';
if(isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])){
  $view = !isset($_POST['wishlist']);
    if(isset($_POST['wishlist']) && !empty($_POST['wishlist'])) {
        echo "<head>";
        echo "<title>Product</title>";
        echo "</head>";
        echo "<link rel='stylesheet' href='style.css'>";
        echo "<div class='topnav'>";
        echo "<a href='landing.php'>Profile</a>";
        echo "<a href='logout.php'>Logout</a>";
        echo "<a href='browse.php'>Browse Product</a>";
        echo "<div class='topnav-right'>";
        echo "<a href='notifications.php'>Notifications</a>";
        echo "<a href='reviews.php'>Reviews</a>";
        echo "<a href='wishlist.php'>Wishlist</a>";
        echo "<a href='compare.php'>Compare</a>";
        echo "<a href='recommendation.php'>Recommendation</a>";
        echo "</div>";
        echo "</div>";
        $wishlist = $_POST['wishlist'];
        $username = $_SESSION['username'];
        $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
        $request = array();
        $request['type'] = "Wishlist";
        $request['username'] = $username;
        $request['wishlist'] = $wishlist;
        $response = $client->send_request($request);
        if($response['returnCode'] == 1 && $response['message'] == 'Wishlist stored successfully') {
            echo "Wishlist submitted successfully.";
        } else {
            echo "Error storing wishlist.";
        }
    }else if(isset($_POST['removeItem']) && !empty($_POST['removeItem'])){
        $itemToRemove = $_POST['removeItem'];
        $username = $_SESSION['username'];
        $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
        $request = array();
        $request['type'] = "ClearWishlist";
        $request['username'] = $username;
        $request['itemTitle'] = $itemToRemove;
        $response = $client->send_request($request);
        if($response['returnCode'] == 1) {
            header("Location: wishlist.php");
            exit();
        } else {
            echo "Error removing item from wishlist.";
        }
    }else {
        $username = $_SESSION['username'];
        $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
        $request = array();
        $request['type'] = "FetchWishlist";
        $request['username'] = $username;
        $response = $client->send_request($request);
        if($response['returnCode'] == 1) {
            echo "<link rel='stylesheet' href='style.css'>";
            echo "<div class='topnav'>";
            echo "<a href='landing.php'>Profile</a>";
            echo "<a href='logout.php'>Logout</a>";
            echo "<a href='browse.php'>Browse Product</a>";
            echo "<div class='topnav-right'>";
            echo "<a href='notifications.php'>Notifications</a>";
            echo "<a href='reviews.php'>Reviews</a>";
            echo "<a href='wishlist.php'>Wishlist</a>";
            echo "<a href='recommendation.php'>Recommendation</a>";
            echo "</div>";
            echo "</div>";
            echo "<h2>Your Wishlist:</h2>";
            echo "<form action='wishlist.php' method='post'>";
            echo "<ul>";
            foreach($response['wishlist'] as $item) {
              $words = explode(" ", $item['ItemTitle']);
            echo "<li>" . $item['ItemTitle'] . " - $" . $item['Price'] . " - <a href='" . $item['ItemURL'] . "' target='_blank'>View Item</a> ";
            echo "<form action='wishlist.php' method='post' id='formReview'>";
            echo "<input type='hidden' name='removeItem[]' value='" . $item['ItemTitle'] . "' />";
            echo "<input type='submit' value='Remove From Wishlist' class='btn-remove'/>";
            echo "</form>";
            echo "<div class='review-form'>";
            echo "<form action='submit_review.php' method='post'>";
            echo "<input type='hidden' name='itemTitle' value='" . $item['ItemTitle'] . "' />";
            echo "<textarea name='review' placeholder='Write your review...' maxlength='255'></textarea>";
            echo "<input type='submit' value='Submit Review' class='btn-submit-review'>";
            echo "</form>";
            echo "</div>";
            echo "</li>";
            echo "</li>";
            }
             $wordsArray = array();
            foreach ($response['wishlist'] as $item) {
                $words = explode(" ", $item['ItemTitle']);
                foreach ($words as $word) {
                    $word = strtolower($word);
                    if (!isset($wordsArray[$word])) {
                        $wordsArray[$word] = 1;
                    } else {
                        $wordsArray[$word]++;
                    }
                }
            }
            arsort($wordsArray);
            $commonWordsDisplay = 1;
            $commonWords = array_slice($wordsArray, 0, $commonWordsDisplay, true);
            foreach ($commonWords as $word => $freq) {
                if ($freq <= 1) {
                    break;
                } else {
                    $client = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
                    $request = array();
                    $request['type'] = "Recommendation";
                    $request['username'] = $username;
                    $request['commonWord'] = $word;
                    $response = $client->send_request($request);
                    if ($response['returnCode'] == 1) {
                        $_SESSION['recommendation'] = $response['responseData'];
                        exit();
                    }
                }
            }
        } else {
            echo "Error retrieving wishlist data.";
        }
      }
} else {
    header("Location: login.php");
    exit();
}
?>
