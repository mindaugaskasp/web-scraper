<?php
declare(strict_types=1);

namespace App\Services\ScanManager;

use App\Services\Websites\WebsiteInterface;

interface ScanManagerInterface
{
    public static function getContainerId(): string;

    public function getRescanTimeSeconds(): int;
    public function addWebsite(WebsiteInterface $website): ScanManagerInterface;
    public function scan(): ScanResultInterface;
    public function isScanOver(): bool;
}
