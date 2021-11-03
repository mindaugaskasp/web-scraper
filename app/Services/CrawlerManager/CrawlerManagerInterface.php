<?php
declare(strict_types=1);

namespace App\Services\CrawlerManager;

interface CrawlerManagerInterface
{
    public function setWebsites(array $websites): CrawlerManagerInterface;
    public function crawl(): void;
}
