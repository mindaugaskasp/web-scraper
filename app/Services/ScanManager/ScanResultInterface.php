<?php
declare(strict_types=1);

namespace App\Services\ScanManager;

use App\Services\Websites\Data\ProductInterface;

interface ScanResultInterface
{
    public function addProduct(ProductInterface $product): ScanResultInterface;
    public function getProducts(): array;
    public function getProductCount(): int;
}
