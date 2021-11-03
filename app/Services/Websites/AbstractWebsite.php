<?php
declare(strict_types=1);

namespace App\Services\Websites;

abstract class AbstractWebsite implements WebsiteInterface
{
    private $keywords;

    abstract public static function getHost(): string;

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): WebsiteInterface
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public static function getProtocol(): string
    {
        return 'https://';
    }
}
