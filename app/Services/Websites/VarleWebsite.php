<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class VarleWebsite extends AbstractWebsite
{
    public static function getHost(): string
    {
        return 'www.varle.lt';
    }

    public function getUrls(): array
    {
        $urls = [];

        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::getProtocol() . self::getHost() . '/search/?q=' . $keyword;
        }

        return $urls;
    }

    public function getProductsByResponse(ResponseInterface $response): array
    {
        $products = [];

        $crawler = (new Crawler((string) $response->getBody()));
        if ($crawler->filter('.count.total-numFound')->first()->text() == '()') {
            return [];
        }

        $rows = $crawler->filter('.box1 .grid-item.product');

        $rows->each(
            function (Crawler $node) use (&$products) {
                if ($node->filter('.price')->count() === 0) {
                    return;
                }

                $price = $node->filter('.price')->first()->text();
                $name = $node->filter('.title')->first()->text();

                if ($node->filter('.title')->filter('a')->count() === 0) {
                    return;
                }

                $url = self::getProtocol()
                    . self::getHost()
                    . '/'
                    . $node->filter('.title')->first()->filter('a')->attr('href');

                $product = new Product(
                    $name,
                    $url,
                    $price,
                    '?',
                    self::getHost()
                );

                $products[] = $product;
            }
        );

        return $products;
    }
}
