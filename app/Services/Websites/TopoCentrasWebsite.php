<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class TopoCentrasWebsite extends AbstractWebsite
{
    public static function getHost(): string
    {
        return 'www.topocentras.lt';
    }

    public function getUrls(): array
    {
        $urls = [];

        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::getProtocol() . self::getHost() . '/catalogsearch/result/?q=' . $keyword;
        }

        return $urls;
    }

    public function getProductsByResponse(ResponseInterface $response): array
    {
        $products = [];

        $crawler = (new Crawler((string) $response->getBody()));

        $main = $crawler->filter('.CategoryPage-mainCategory-NcP');
        if ($main->count() === 0) {
            return [];
        }

        $rows = $main
            ->first()
            ->filter('.CategoryPage-mainCategory-NcP')
            ->filter('.ProductGrid-catalogProductGrid-3ct')
            ->children();

        $rows->each(
            function (Crawler $node) use (&$products) {
                $price = $node->filter('.ProductGrid-productPrice-1wU')->first()->text();
                $name = $node->filter('.ProductGrid-productName-1JN')->first()->text();

                $url = self::getProtocol()
                    . self::getHost()
                    . $node->filter('.ProductGrid-link-3Q6')->first()->filter('a')->attr('href');

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
