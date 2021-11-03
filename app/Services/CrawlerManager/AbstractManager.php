<?php
declare(strict_types=1);

namespace App\Services\CrawlerManager;

use App\Services\Cache\ProductCache;
use App\Services\Crawler\CrawlerObserver;
use App\Services\Logger\Logger;

abstract class AbstractManager implements CrawlerManagerInterface
{
    protected $websites = [];
    protected $cache;
    protected $crawlerObserver;
    protected $logger;

    public function __construct(ProductCache $cache, CrawlerObserver $crawlerObserver, Logger $logger)
    {
        $this->cache = $cache;
        $this->crawlerObserver = $crawlerObserver;
        $this->logger = $logger;
    }

    abstract public function crawl(): void;

    public function setWebsites(array $websites): CrawlerManagerInterface
    {
        $this->websites = $websites;
        return $this;
    }
}
