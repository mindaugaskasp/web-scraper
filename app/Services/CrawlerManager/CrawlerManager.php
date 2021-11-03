<?php
declare(strict_types=1);

namespace App\Services\CrawlerManager;

use App\Services\Websites\AbstractWebsite;
use Monolog\Logger;
use Spatie\Crawler\Crawler as CrawlerHandler;
use Throwable;

class CrawlerManager extends AbstractManager
{
    public function crawl(?callable $callback = null): void
    {
        $urls = [];

        $handler = CrawlerHandler::create()
            ->executeJavaScript()
            ->setDelayBetweenRequests(2000)
            ->ignoreRobots()
            ->setConcurrency(1)
            ->setMaximumCrawlCount(1)
            ->setMaximumDepth(1)
            ->addCrawlObserver(
                $this->crawlerObserver->setCrawledCallback($callback)
            );

        /** @var AbstractWebsite $website */
        foreach ($this->websites as $website) {
            if (!$website->isEnabled()) {
                continue;
            }
            $urls = array_merge($urls, $website->getUrls());
        }

        foreach ($urls as $url) {
            try {
                $handler->startCrawling($url);
            } catch (Throwable $e) {
                $this->logger->log($e->getMessage(), Logger::ERROR);
            }
        }
    }
}
