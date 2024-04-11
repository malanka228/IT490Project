<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Comparison</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class='topnav'>
        <a href='landing.php'>Profile</a>
        <a href='logout.php'>Logout</a>
        <a href='browse.php'>Browse Product</a>
        <div class='topnav-right'>
            <a href='reviews.php'>Reviews</a>
            <a href='wishlist.php'>Wishlist</a>
        </div>
    </div>
    <h1>Comparison Results</h1>
    <div class="comparison-cont">
        <?php
        if (isset($_POST['compareValues'])) {
            $compareValues = $_POST['compareValues'];
            $selectedItems = explode(',', $compareValues);
            $numItems = count($selectedItems);
            $containerWidth = 100 / $numItems;
            foreach ($selectedItems as $item) {
                $itemDetails = explode('|', $item);
                $image = $itemDetails[0];
                $title = $itemDetails[1];
                $price = $itemDetails[2];
                $rating = isset($itemDetails[3]) && !empty($itemDetails[3]) ? $itemDetails[3] : '';
                $url = $itemDetails[4];
                $shortenedUrl = substr($url, 0, 30) . '...';
                echo "<div class='product-cont' style='width: $containerWidth%;'>";
                echo "<img src='$image' alt='Product Image'>" . "<br>";
                echo "<div class='product-det'>";
                echo "<b>Title:</b> $title<br>";
                echo "<b>Price:</b> $price<br>";
                if (!empty($rating)) {
                    echo "<b>Rating:</b> $rating<br>";
                }
                echo "<b>URL:</b> <a href='$url' target='_blank'>$shortenedUrl</a><br>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No items selected for comparison.</p>";
        }
        ?>
    </div>
</body>
</html>
