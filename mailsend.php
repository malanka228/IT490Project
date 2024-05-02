<?php

$to = "ag722@njit.edu";
$subject = "PricifyInspect Price Alert";
$message = "Hi there! An item you have wishlisted has now dropped to a lower price!";
$headers = "From: sender@example.com";

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}