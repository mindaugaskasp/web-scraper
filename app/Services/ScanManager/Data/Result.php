<?php
declare(strict_types=1);

namespace App\Services\ScanManager\Data;

use App\Services\ScanManager\ScanResultInterface;
use App\Services\Websites\Data\ProductInterface;

class Result implements ScanResultInterface
{
    /**
     * @var array[ProductInterface]
     */
    private $products = [];

    public function addProduct(ProductInterface $product): ScanResultInterface
    {
        $this->products[] = $product;
        return $this;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProductCount(): int
    {
        return count($this->products);
    }
}
