<?php
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class EmailNotifier {

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }
    public function sendConfirmationEmail($email) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_EMAIL'], 'Price Tracker');
            $mail->addAddress($email);
            $mail->Subject = 'Please Confirm Your Subscription';

            $token = bin2hex(random_bytes(16));

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
            $domain = $_SERVER['HTTP_HOST'];

            $confirmationLink = "$protocol://$domain/confirm.php?token=$token";

            $mail->Body = "Please click the following link to confirm your subscription: $confirmationLink";

            $mail->send();
            echo 'Confirmation email sent!';

            $this->saveConfirmationToken($email, $token);

        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    public function sendPriceChangeNotification($email, $adLink, $newPrice) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom('no-reply@example.com', 'Price Tracker');
            $mail->addAddress($email);

            $mail->Subject = 'Price Change Detected';
            $mail->Body = "The price for the ad ($adLink) has changed to $newPrice.";

            $mail->send();
            echo 'Price change notification sent!';

        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }
    private function saveConfirmationToken($email, $token) {
        $db = new Db();
        $db->query('UPDATE subscriptions SET token = ? WHERE email = ?', [$token, $email]);
    }
}
