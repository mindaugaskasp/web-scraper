<?php
declare(strict_types=1);

namespace App\Services\Crawler;

use App\Services\Cache\ProductCache;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObserver;

abstract class AbstractCrawler extends CrawlObserver
{
    protected $cache;
    protected $crawledCallback;

    public function __construct(ProductCache $cache)
    {
        $this->cache = $cache;
    }

    public function setCrawledCallback(callable $callback): AbstractCrawler
    {
        $this->crawledCallback = $callback;
        return $this;
    }

    abstract public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null);

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        throw $requestException;
    }
}
