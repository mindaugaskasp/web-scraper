<?php
declare(strict_types=1);

namespace App\Services\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer implements StockMailInterface
{
    private const DEFAULT_GOOGLE_SMTP_PORT = 465;

    public function send(string $subject, string $html, string $recipient): void
    {
        //todo control debug calls via APP_DEBUG env instead
        //todo inject PHPMailer class
        //todo inject .env properly

        $mail = new PHPMailer(true);
        $mail->IsSMTP();

        //Server settings
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = self::DEFAULT_GOOGLE_SMTP_PORT;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false,'verify_peer_name'  => false, 'allow_self_signed' => true]];
        $mail->Username = $_ENV['SMTP_GOOGLE_USERNAME'];
        $mail->Password = $_ENV['SMTP_GOOGLE_PASSWORD'];
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->addAddress($recipient);
        $mail->setFrom($_ENV['SMTP_GOOGLE_USERNAME'], 'Web scanner');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->send();
    }
}
