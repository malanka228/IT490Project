<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class='topnav'>
    <a href='landing.php'>Profile</a>
    <a href='logout.php'>Logout</a>
    <a href='browse.php'>Browse Product</a>
    <div class='topnav-right'>
    <a href='notification.html'>Notifications</a>
    <a href='reviews.html'>Reviews</a>
    <a href='wishlist.php'>Wishlist</a>
    </div>
    </div>
    <div class="back-button">
    <a href="browse.php">&larr; Back to Search</a>
    </div>
<?php
    session_start();
    if(isset($_SESSION['responseData'])){
        $results = $_SESSION['responseData'];
        $displayedResults = 0;
        $displayedResults = 0;
            foreach ($results as $result) {
                $data1 = $result['price'];
                $data2 = $result['title'];
                $data3 = $result['rating'];
                $data4 = $result['image'];
                $data5 = $result['url'];
                $shortenedUrl = substr($data5, 0, 30) . '...';
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
                if ($displayedResults >= 5) {
                    break;
                }
        }
        unset($_SESSION['responseData']);
    } else {
        echo " No response";
    }
    
?>
</body>
</html>
