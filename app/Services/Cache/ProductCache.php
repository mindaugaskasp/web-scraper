<?php
declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\Websites\Data\Product;
use Psr\Cache\InvalidArgumentException;

class ProductCache extends AbstractCache
{
    public function getCacheNameSpace(): string
    {
        return 'products';
    }

    public function getCacheKeyForProduct(Product $product): string
    {
        return md5($product->getUrl());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheProduct(Product $product): void
    {
        $item = $this->cache->getItem($this->getCacheKeyForProduct($product));
        $item->set($product);
        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isProductCached(Product $product): bool
    {
        return $this->cache->hasItem($this->getCacheKeyForProduct($product));
    }
}
