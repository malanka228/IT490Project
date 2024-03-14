<!DOCTYPE html>
<html lang="en">
<html> 
  <head>
    <title>Landing</title>
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
        echo "<a href='notification.html'>Notifications</a>";
        echo "<a href='reviews.html'>Reviews</a>";
        echo "<a href='wishlist.html'>Wishlist</a>";
        echo "</div>";
        echo "</div>";
        echo "<p>Welcome to the landing page, " . $_SESSION['username'] . "</p>";
      } else {
        header("Location: login.html");
        exit();
      }
    ?>
</body>
</html>