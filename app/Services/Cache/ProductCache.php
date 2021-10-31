<?php
declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\Websites\Data\Product;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Cache\InvalidArgumentException;

class ProductCache
{
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter('products', 1800, $this->getCacheDir());
    }

    public function getCacheKeyForProduct(Product $product): string
    {
        return md5($product->getUrl());
    }

    public function getCacheDir(): string
    {
        return getcwd() . '/storage/cache';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheProduct(Product $product): void
    {
        $this->cache->get(
            $this->getCacheKeyForProduct($product),
            function () use ($product) {
                return $product;
            }
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isProductCached(Product $product): bool
    {
        return $this->cache->hasItem($this->getCacheKeyForProduct($product));
    }
}
