<?php
declare(strict_types=1);

namespace App\Services\ScanManager;

use App\Services\Cache\ProductCache;
use App\Services\Websites\WebsiteInterface;

abstract class AbstractManager implements ScanManagerInterface
{
    protected $scanOver = false;
    protected $websites;

    private $cache;
    private $rescanTimeSeconds;

    public function __construct(ProductCache $cache, int $rescanTimeSeconds)
    {
        $this->cache = $cache;
        $this->rescanTimeSeconds = $rescanTimeSeconds;
    }

    abstract public function scan(): ScanResultInterface;

    public function addWebsite(WebsiteInterface $website): ScanManagerInterface
    {
        $this->websites[] = $website;
        return $this;
    }

    public function getRescanTimeSeconds(): int
    {
        return $this->rescanTimeSeconds;
    }

    protected function getProductCache(): ProductCache
    {
        return $this->cache;
    }

    protected function setScanOver(): ScanManagerInterface
    {
        $this->scanOver = true;
        return $this;
    }

    public function isScanOver(): bool
    {
        return $this->scanOver;
    }

    public static function getContainerId(): string
    {
        return 'manager';
    }
}
