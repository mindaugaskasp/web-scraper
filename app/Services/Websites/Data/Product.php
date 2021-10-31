<?php
declare(strict_types=1);

namespace App\Services\Websites\Data;

class Product implements ProductInterface
{
    private $name;
    private $url;
    private $price;
    private $quantity;
    private $websiteUrl;

    public function __construct(string $name, string $url, string $price, string $quantity, string $websiteUrl)
    {
        $this->name = $name;
        $this->url = $url;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->websiteUrl = $websiteUrl;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }
}
