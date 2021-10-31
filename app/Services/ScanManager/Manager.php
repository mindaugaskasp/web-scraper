<?php
declare(strict_types=1);

namespace App\Services\ScanManager;

use App\Services\ScanManager\Data\Result;
use App\Services\Websites\Data\Product;
use App\Services\Websites\WebsiteInterface;
use Goutte\Client;

class Manager extends AbstractManager
{
    public function scan(): ScanResultInterface
    {
        $client = new Client();
        $result = new Result();

        /** @var WebsiteInterface $website */
        foreach ($this->websites as $website) {
            $crawler = $client->request('GET', $website->getUrl());
            $website->runCrawler(
                $crawler,
                function (Product $product) use ($result) {
                    $result->addProduct($product);
                    $this->setScanOver();
                }
            );
        }

        return $result;
    }
}
