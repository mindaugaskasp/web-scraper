<?php
declare(strict_types=1);

namespace App\Services\Mail;

interface StockMailInterface
{
    public function send(string $subject, string $html, string $recipient): void;
}
