<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $listName = $_POST['listName'];
    $privacy = $_POST['privacy'];

    // Process form data (for example, save to database)
    // Here, you can perform database operations to save the list data
    // Replace this with your actual database connection and query
    // Example:
    // $servername = "localhost";
    // $username = "username";
    // $password = "password";
    // $dbname = "database";
    // $conn = new mysqli($servername, $username, $password, $dbname);
    // $sql = "INSERT INTO lists (list_name, privacy) VALUES ('$listName', '$privacy')";
    // $result = $conn->query($sql);

    // Redirect back to the HTML page
    header("Location: createList.html");
    exit();
} else {
    // If the form is not submitted, redirect back to the HTML page
    header("Location: createList.html");
    exit();
}
?>