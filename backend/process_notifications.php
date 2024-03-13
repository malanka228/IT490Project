<?php

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the notifications checkbox is checked
    if (isset($_POST['notifications']) && $_POST['notifications'] == "on") {
        // Save the user's preference to turn on notifications
        // For demonstration purposes, let's just print a message
        echo "Notifications turned on for wishlist!";
    } else {
        // Save the user's preference to turn off notifications
        // For demonstration purposes, let's just print a message
        echo "Notifications turned off for wishlist!";
    }
} else {
    // If the form is not submitted, redirect to the home page or display an error message
    header("Location: index.html"); // Redirect to the home page
    exit;
}

?>