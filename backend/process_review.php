<?php

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the inputs
    $reviewTitle = trim($_POST["reviewTitle"]);
    $reviewText = trim($_POST["reviewText"]);
    $rating = $_POST["rating"];

    // Check if the title and review text are not empty
    if (empty($reviewTitle) || empty($reviewText)) {
        // Handle empty fields error
        echo "Title and review text are required.";
        // You might want to redirect the user back to the form or display an error message
        exit;
    }

    // Check if the rating is within the allowed range
    if ($rating < 1 || $rating > 5) {
        // Handle invalid rating error
        echo "Rating must be between 1 and 5.";
        // You might want to redirect the user back to the form or display an error message
        exit;
    }

    // Process the review data (for demonstration purposes, we'll just print it)
    echo "Review Title: " . $reviewTitle . "<br>";
    echo "Review Text: " . $reviewText . "<br>";
    echo "Rating: " . $rating . "<br>";

    // You can further process the review data here, such as saving it to a database

} else {
    // If the form is not submitted via POST method, redirect the user back to the form
    header("Location: create_review.html"); // Change this to the actual filename of your form
    exit;
}

?>