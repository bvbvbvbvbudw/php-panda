<?php
require_once '../src/Db.php';
require_once '../src/SubscriptionController.php';

$token = $_GET['token'] ?? null;
$message = '';

if ($token) {
    $controller = new SubscriptionController();
    $controller->confirmSubscription($token);
    $message = "Your subscription has been confirmed!";
} else {
    $message = "Invalid or missing token!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Confirmation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Subscription Status</h1>
    <p><?php echo $message; ?></p>
    <a href="index.php" class="back-button">Go Back to Home</a>
</div>
</body>
</html>
