<?php
declare(strict_types=1);

namespace App\Services\Websites\Tech;

use App\Services\Websites\AbstractWebsite;
use App\Services\Websites\Data\Product;
use Symfony\Component\DomCrawler\Crawler;

class SkytechWebsite extends AbstractWebsite
{
    private const BASE_URL = 'https://www.skytech.lt/';

    public function getUrl(): string
    {
        $keywords = implode('|', $this->getKeywords());

        return self::BASE_URL
            . 'search.php?sand=1&sort=5a&grp=1&keywords='
            . urlencode($keywords)
            . '&search_in_description=0&pagesize=500&f=86_85';
    }

    public static function getContainerId(): string
    {
        return 'skytech';
    }

    public function runCrawler(Crawler $crawler, callable $callback): void
    {
        $rows = $crawler
            ->filter('.contentbox-center-wrap.nopad')
            ->filter('table')
            ->filter('tr')
        ;

        $rows->each(
            function (Crawler $node, int $i) use ($callback) {
                if ($i === 0) {
                    return;
                }

                $children = $node->children();
                if ($children->count() === 1) {
                    return;
                }
                if ($children->filter('.name')->count() === 0) {
                    return;
                }
                if ($children->filter('strong')->count() === 0) {
                    return;
                }

                if (is_callable($callback)) {
                    $callback($this->makeProductFromChildren($children));
                }
            }
        );
    }

    private function makeProductFromChildren(Crawler $children): Product
    {
        $name = $children->filter('.name')->first()->text();
        $price = $children->filter('strong')->first()->text();
        $relativeUrl = $children->filter('.name')->first()->filter('a')->attr('href');
        $fullUrl = self::BASE_URL . $relativeUrl;
        $quantity = $children->filter('td.kiekis')->first()->text();

        return new Product(
            $name,
            $fullUrl,
            $price,
            $quantity,
            self::BASE_URL
        );
    }
}
