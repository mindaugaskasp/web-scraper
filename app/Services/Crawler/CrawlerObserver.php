<?php
declare(strict_types=1);

namespace App\Services\Crawler;

use App\Services\Websites\WebsiteFactory;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class CrawlerObserver extends AbstractCrawler
{
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        throw $requestException;
    }

    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $products = WebsiteFactory::make($url->getHost())->getProductsByResponse($response);
        foreach ($products as $index => &$product) {
            $this->cache->isProductCached($product);
            if ($this->cache->isProductCached($product)) {
                unset($products[$index]);
            } else {
                $this->cache->cacheProduct($product);
            }
        }

        if (is_callable($this->crawledCallback)) {
            call_user_func($this->crawledCallback, $url, $products);
        }
    }
}
