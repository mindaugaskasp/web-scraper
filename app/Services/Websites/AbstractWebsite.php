<?php
declare(strict_types=1);

namespace App\Services\Websites;

abstract class AbstractWebsite implements WebsiteInterface
{
    private $keywords = [];

    public function addKeyword(string $keyword): WebsiteInterface
    {
        $this->keywords[] = $keyword;
        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function requiresMultipleRequests(): bool
    {
        return false;
    }

    /**
     * Override If Website requires ssingle request for keyword list
     */
    public function getUrl(): string
    {
        return '';
    }

    /**
     * Override If Website requires multiple requests for keyword list
     */
    public function getUrls(): array
    {
        return [];
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
