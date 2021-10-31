<?php
declare(strict_types=1);

namespace App\Services\ScanManager;

use App\Services\ScanManager\Data\Result;
use App\Services\Websites\Data\Product;
use App\Services\Websites\WebsiteInterface;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Manager extends AbstractManager
{
    public function scan(): ScanResultInterface
    {
        $client = new Client();
        $result = new Result();

        /** @var WebsiteInterface $website */
        foreach ($this->websites as $website) {
            if (!$website->isEnabled()) {
                continue;
            }
            if (!$website->requiresMultipleRequests()) {
                $crawler = $client->request('GET', $website->getUrl());
                $this->singleWebsiteRequest($website, $crawler, $result);
                continue;
            }

            $this->multipleWebsiteRequests($website, $client, $result);
        }

        return $result;
    }

    private function singleWebsiteRequest(WebsiteInterface $website, Crawler $crawler, Result $result): void
    {
        $website->runCrawler(
            $crawler,
            function (Product $product) use ($result) {
                // add product to cache, so we notify only non cached new stock updates
                if (!$this->getProductCache()->isProductCached($product)) {
                    $this->getProductCache()->cacheProduct($product);
                    $result->addProduct($product);
                }
            }
        );
    }

    /**
     * Some retailer sites have crappy search implemented that do not accept multiple keyword search
     * in those cases multiple requests for each keyword are made instead.
     */
    private function multipleWebsiteRequests(WebsiteInterface $website, Client $client, Result $result): void
    {
        $urls = $website->getUrls();
        foreach ($urls as $url) {
            $crawler = $client->request('GET', $url);
            $this->singleWebsiteRequest($website, $crawler, $result);
        }
    }
}
