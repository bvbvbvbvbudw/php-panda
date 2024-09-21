<?php
require_once 'Db.php';
require_once 'EmailNotifier.php';

class PriceChecker {
    private $db;
    private $notifier;

    public function __construct() {
        $this->db = new Db();
        $this->notifier = new EmailNotifier();
    }
    public function checkForPriceChanges() {
        $subscriptions = $this->db->query('SELECT DISTINCT ad_link FROM subscriptions WHERE user_confirmed = 1')->fetchAll(PDO::FETCH_ASSOC);

        foreach ($subscriptions as $subscription) {
            $adLink = $subscription['ad_link'];
            $currentPrice = $this->getPriceFromAd($adLink);
            $priceChanged = $this->updatePriceData($adLink, $currentPrice);
            if ($priceChanged) {
                $users = $this->db->query('SELECT email FROM subscriptions WHERE ad_link = ? AND user_confirmed = 1', [$adLink])->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as $user) {
                    $this->notifier->sendPriceChangeNotification($user['email'], $adLink, $currentPrice);
                }
            }
        }
    }

    private function updatePriceData($adLink, $currentPrice) {
        $existingPrice = $this->db->query('SELECT last_price FROM price_data WHERE ad_link = ?', [$adLink])->fetchColumn();
        $priceChanged = false;

        if ($existingPrice === false) {
            $this->db->query('INSERT INTO price_data (ad_link, last_price) VALUES (?, ?)', [$adLink, $currentPrice]);
            $priceChanged = true;
        } else if ($existingPrice != $currentPrice) {
            // Обновляем цену
            $this->db->query('UPDATE price_data SET last_price = ? WHERE ad_link = ?', [$currentPrice, $adLink]);
            $priceChanged = true;
        }

        return $priceChanged;
    }


    private function getPriceFromAd($adLink) {
        $htmlContent = file_get_contents($adLink);
        if ($htmlContent === FALSE) {
            throw new Exception('Failed to fetch ad page.');
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($htmlContent);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//div[contains(@class, "css-e2ir3r")]//h3[contains(@class, "css-90xrc0")]');

        if ($nodes->length > 0) {
            $priceText = $nodes->item(0)->nodeValue;
            $price = preg_replace('/[^\d.,]/', '', $priceText);
            return (float) str_replace(',', '.', $price);
        } else {
            throw new Exception('Price element not found.');
        }
    }
}
