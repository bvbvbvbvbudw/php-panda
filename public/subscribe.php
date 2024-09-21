<?php
require '../src/Db.php';
require '../src/SubscriptionController.php';

$adLink = $_POST['ad_link'] ?? null;
$email = $_POST['email'] ?? null;
$message = '';

$adLink = filter_var($adLink, FILTER_SANITIZE_URL);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if (filter_var($adLink, FILTER_VALIDATE_URL) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (preg_match('/^(https?:\/\/)?(www\.)?olx\.\S+$/i', $adLink)) {
        $controller = new SubscriptionController();
        $controller->subscribe($adLink, $email);
        $message = "Subscription successful! Please check your email to confirm the subscription.";
    } else {
        $message = "Invalid OLX ad link!";
    }
} else {
    $message = "Invalid input! Please provide both a valid ad link and email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Result</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Subscription Status</h1>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="index.php" class="back-button">Go Back to Home</a>
</div>
</body>
</html>
