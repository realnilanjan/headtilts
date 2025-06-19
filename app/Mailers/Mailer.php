<?php

namespace App\Mailers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'localhost';
            $this->mailer->Port       = (int) ($_ENV['MAIL_PORT'] ?? 1025);
            $this->mailer->SMTPAuth   = false; // MailHog doesn't need auth
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] === 'null' ? false : $_ENV['MAIL_ENCRYPTION'];

            // Sender
            $this->mailer->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@headtilts.test',
                $_ENV['MAIL_FROM_NAME'] ?? 'Headtilts'
            );
        } catch (\Exception $e) {
            error_log("Mailer Error during setup: " . $e->getMessage());
        }
    }

    public function send(string $to, string $subject, string $htmlContent): bool
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlContent;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
