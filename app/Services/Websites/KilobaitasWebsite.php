<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Symfony\Component\DomCrawler\Crawler;

class KilobaitasWebsite extends AbstractWebsite
{
    private const BASE_URL = 'https://www.kilobaitas.lt/';

    public function requiresMultipleRequests(): bool
    {
        return true;
    }

    public function getUrls(): array
    {
        $urls = [];

        // Note: for some reason search breaks on more than one keyword at time
        // so mitigate this we break down keywords into chunks of 2 and make several requests
        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::BASE_URL . 'paieskos_rezultatai/searchresult.aspx?q=' . $keyword;
        }

        return $urls;
    }

    public function runCrawler(Crawler $crawler, callable $callback): void
    {
        $rows = $crawler->filter('.products-grid.row');;

        $productsFound = $rows->count() !== 0;
        if (!$productsFound) {
            return;
        }

        $rows->each(
            function (Crawler $node) use ($callback) {
                $price = $node->filter('.products-grid.row')->filter('meta')->last()->attr('content');
                $name = $node->filter('.products-grid.row')->filter('.item-title.line-clamp a')->text();;
                $url = self::BASE_URL . $node->filter('.products-grid.row')->filter('.item-title.line-clamp a')->attr('href');
                $quantity = $node->filter('.products-grid.row')->filter('p.availability.in-stock')->filter('.btn-blue.item-code')->last()->text();

                $product = new Product(
                    $name,
                    $url,
                    $price,
                    $quantity,
                    $node->getBaseHref()
                );

                $callback($product);
            }
        );
    }
}
