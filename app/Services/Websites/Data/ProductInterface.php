<?php
declare(strict_types=1);

namespace App\Services\Websites\Data;

interface ProductInterface
{
    public function getName(): string;
    public function getUrl(): string;
    public function getPrice(): string;
    public function getQuantity(): string;
    public function getWebsiteUrl(): string;
}
