<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Symfony\Component\DomCrawler\Crawler;

class VarleWebsite extends AbstractWebsite
{
    private const BASE_URL = 'https://www.varle.lt';

    public function requiresMultipleRequests(): bool
    {
        return true;
    }

    public function getUrls(): array
    {
        $urls = [];

        // Note: for some reason Varle search breaks on more than one keyword at time
        // so mitigate this we break down keywords into chunks of 2 and make several requests
        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::BASE_URL . '/search/?q=' . $keyword;
        }

        return $urls;
    }

    public function runCrawler(Crawler $crawler, callable $callback): void
    {
        $productsFound = $crawler->filter('.count.total-numFound')->first()->text() !== '()';
        if (!$productsFound) {
            return;
        }

        $rows = $crawler->filter('.box1 .grid-item.product');

        $rows->each(
            function (Crawler $node) use ($callback) {
                if ($node->filter('.price')->count() === 0) {
                    return;
                }

                $price = $node->filter('.price')->first()->text();
                $name = $node->filter('.title')->first()->text();

                if ($node->filter('.title')->filter('a')->count() === 0) {
                    return;
                }

                $url = self::BASE_URL . $node->filter('.title')->first()->filter('a')->attr('href');

                $product = new Product(
                    $name,
                    $url,
                    $price,
                    '?',
                    $node->getBaseHref()
                );

                $callback($product);
            }
        );
    }
}
