<?php
declare(strict_types=1);

namespace App\Services\Websites;

use Symfony\Component\DomCrawler\Crawler;

interface WebsiteInterface
{
    public function getUrl(): string;
    public function getKeywords(): array;
    public function addKeyword(string $keyword): WebsiteInterface;
    public function runCrawler(Crawler $crawler, callable $callback): void;
}
