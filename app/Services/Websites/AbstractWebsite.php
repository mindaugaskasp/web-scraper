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
}
