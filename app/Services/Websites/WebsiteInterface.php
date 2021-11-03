<?php
declare(strict_types=1);

namespace App\Services\Websites;

use Psr\Http\Message\ResponseInterface;

interface WebsiteInterface
{
    public function getUrls(): array;
    public function getKeywords(): array;
    public function setKeywords(array $keywords): WebsiteInterface;
    public function isEnabled(): bool;
    public function getProductsByResponse(ResponseInterface $response): array;
}
