<?php
declare(strict_types=1);

namespace App\Services\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

abstract class AbstractCache
{
    protected $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter($this->getCacheNameSpace(), 1800, $this->getCacheDir());
    }

    abstract public function getCacheNameSpace(): string;

    protected function getCacheDir(): string
    {
        return getcwd() . '/storage/cache';
    }

    public function clear(): AbstractCache
    {
        $this->cache->clear();
        return $this;
    }
}
