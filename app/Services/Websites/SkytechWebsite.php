<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class SkytechWebsite extends AbstractWebsite
{
    public static function getHost(): string
    {
        return 'www.skytech.lt';
    }

    public function getUrls(): array
    {
        $keywords = implode('|', $this->getKeywords());

        $url = self::getProtocol()
            . self::getHost()
            . '/search.php?sand=1&sort=5a&grp=1&keywords='
            . urlencode($keywords)
            . '&search_in_description=0&pagesize=500&f=86_85';

        return [$url];
    }

    public function getProductsByResponse(ResponseInterface $response): array
    {
        $products = [];

        $rows = (new Crawler((string) $response->getBody()))
            ->filter('.contentbox-center-wrap.nopad')
            ->filter('table')
            ->filter('tr');

        $rows->each(
            function (Crawler $node, int $i) use (&$products) {
                $children = $node->children();
                if ($i === 0) {
                    return;
                }
                if ($children->count() === 1) {
                    return;
                }
                if ($children->filter('.name')->count() === 0) {
                    return;
                }
                if ($children->filter('strong')->count() === 0) {
                    return;
                }

                $products[] = $this->makeProductFromChildren($children);
            }
        );

        return $products;
    }

    private function makeProductFromChildren(Crawler $children): Product
    {
        $name = $children->filter('.name')->first()->text();
        $price = $children->filter('strong')->first()->text();
        $relativeUrl = $children->filter('.name')->first()->filter('a')->attr('href');
        $fullUrl = self::getProtocol() . self::getHost() . '/' . $relativeUrl;
        $quantity = $children->filter('td.kiekis')->first()->text();

        return new Product(
            $name,
            $fullUrl,
            $price,
            $quantity,
            self::getHost()
        );
    }
}
