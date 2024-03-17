<!DOCTYPE html>
<html> 
  <head>
    <title>Browse Product</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <?php
      session_start();
      if(isset($_SESSION['jwtToken']) && !empty($_SESSION['jwtToken'])){
      echo "<div class='topnav'>";
      echo "<a href='landing.php'>Profile</a>";
      echo "<a href='logout.php'>Logout</a>";
      echo "<a href='browse.php'>Browse Product</a>";
      echo "<div class='topnav-right'>";
      echo "<a href='landing.php'>Notifications</a>";
      echo "<a href='logout.php'>Reviews</a>";
      echo "<a href='wishlist.php'>Wishlist</a>";
      echo "</div>";
      echo "</div>";
      echo "<div class='product-search'>";
      echo "<form action='search.php' method='post'>";
      echo "<label for='product_name'>Enter Product Name:</label>";
      echo "<input type='text' id='product_name' name='product_name' required>";
      echo "<input name='searchProduct' type='submit' value='Search'>";
      echo "</form>";
      echo "</div>";
      } else {
        header("Location: login.html");
        exit();
      }
    ?>
  </body>
</html>
