<?php
declare(strict_types=1);

namespace App\Services\Mail;

interface MailerInterface
{
    public function send(string $subject, string $html, string $recipient): void;
}
