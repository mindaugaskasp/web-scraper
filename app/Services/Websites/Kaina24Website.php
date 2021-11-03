<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Kaina24Website extends AbstractWebsite
{
    public static function getHost(): string
    {
        return 'www.kaina24.lt';
    }

    public function getUrls(): array
    {
        $urls = [];

        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::getProtocol() . self::getHost() . '/search?q=' . $keyword;
        }

        return $urls;
    }

    public function getProductsByResponse(ResponseInterface $response): array
    {
        $products = [];

        $crawler = (new Crawler((string) $response->getBody()));

        $main = $crawler->filter('.product-list-horisontal');
        if ($main->count() === 0) {
            return [];
        }

        $rows = $main
            ->first()
            ->children();

        $rows->each(
            function (Crawler $node) use (&$products) {
                $price = $node->filter('.price')->first()->text();
                $name = $node->filter('.name')->first()->text();
                $url = $node->filter('.name')->first()->filter('a')->attr('href');

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
