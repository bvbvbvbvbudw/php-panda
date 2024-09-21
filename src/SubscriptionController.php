<?php
require_once 'EmailNotifier.php';
require_once 'Db.php';

class SubscriptionController {
    private $db;
    private $notifier;

    public function __construct() {
        $this->db = new Db();
        $this->notifier = new EmailNotifier();
    }

    public function subscribe($adLink, $email) {
        $token = bin2hex(random_bytes(16));

        $this->db->query(
            'INSERT INTO subscriptions (ad_link, email, user_confirmed, token) VALUES (?, ?, 0, ?)',
            [$adLink, $email, $token]
        );

        $this->notifier->sendConfirmationEmail($email, $token);
    }

    public function confirmSubscription($token) {
        $this->db->query('UPDATE subscriptions SET user_confirmed = 1 WHERE token = ?', [$token]);
    }
}
