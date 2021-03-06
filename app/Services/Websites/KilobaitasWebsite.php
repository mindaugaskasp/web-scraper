<?php
declare(strict_types=1);

namespace App\Services\Websites;

use App\Services\Websites\Data\Product;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class KilobaitasWebsite extends AbstractWebsite
{
    public static function getHost(): string
    {
        return 'www.kilobaitas.lt';
    }

    public function getUrls(): array
    {
        $urls = [];

        foreach ($this->getKeywords() as $keyword) {
            $urls[] = self::getProtocol() . self::getHost() . '/paieskos_rezultatai/searchresult.aspx?q=' . $keyword;
        }

        return $urls;
    }

    public function getProductsByResponse(ResponseInterface $response): array
    {
        $crawler = (new Crawler((string) $response->getBody()));

        $rows = $crawler->filter('.products-grid.row');;
        if ($rows->count() == 0) {
            return [];
        }

        $rows->each(
            function (Crawler $node) use (&$products) {
                $url = self::getProtocol() . self::getProtocol();

                $price = $node->filter('.products-grid.row')->filter('meta')->last()->attr('content');
                $name = $node->filter('.products-grid.row')->filter('.item-title.line-clamp a')->text();;
                $productUrl = $url . '/' . $node->filter('.products-grid.row')->filter('.item-title.line-clamp a')->attr('href');
                $quantity = $node->filter('.products-grid.row')->filter('p.availability.in-stock')->filter('.btn-blue.item-code')->last()->text();

                $product = new Product(
                    $name,
                    $productUrl,
                    $price,
                    $quantity,
                    self::getHost()
                );

                $products[] = $product;
            }
        );

        return $products;
    }
}
