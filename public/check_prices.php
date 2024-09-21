<?php
require_once '../src/Db.php';
require_once '../src/PriceChecker.php';

$priceChecker = new PriceChecker();
$priceChecker->checkForPriceChanges();
?>
