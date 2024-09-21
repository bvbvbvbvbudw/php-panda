<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Price Tracker</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<div class="container">
    <h1>Track OLX Ad Price</h1>

    <form action="subscribe.php" method="POST" onsubmit="return validateForm();">
        <div>
            <label for="ad_link">Ad Link:</label>
            <input type="url" name="ad_link" required>
        </div>
        <div>
            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit">Subscribe</button>
    </form>
</div>
</body>
<script src="public/validate.js"></script>
</html>
