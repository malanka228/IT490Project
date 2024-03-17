<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require 'vendor/autoload.php';

if (isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])) {
    if (isset($_SESSION['recommendation'])) {
        echo "<h1>Recommendation Results</h1>";
        echo "<ul>";
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
        foreach ($_SESSION['recommendation'] as $result) {
            	$data1 = $result['price'];
                $data2 = $result['title'];
                $data3 = $result['rating'];
                $data4 = $result['image'];
                $data5 = $result['url'];
                $shortenedUrl = substr($data5, 0, 30) . '...';
                echo "<link rel='stylesheet' href='style.css'>";
                echo "<div class='product-container'>";
                echo "<img src='$data4' alt='Product Image'>" . "<br>";
                echo "<div class='product-details'>";
                echo "<b>Price:</b> $data1<br>";
                echo "<b>Title:</b> $data2<br>";
                echo "<b>Rating:</b> $data3<br>";
                echo "<b>URL:</b> <a href='$data5' target='_blank'>$shortenedUrl</a><br>";   
                echo "</div>";
                echo "</div>";
                $displayedResults++;
                if ($displayedResults >= 10) {
                    break;
                }
        }
        echo "</ul>";
        unset($_SESSION['recommendation']);
    } else {
        echo "No recommendation results available.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
