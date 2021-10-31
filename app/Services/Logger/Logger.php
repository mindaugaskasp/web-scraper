<?php
declare(strict_types=1);

namespace App\Services\Logger;

use Carbon\Carbon;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

class Logger
{
    private function getFilePathByLevel(int $level): string
    {
        $now = Carbon::now()->toDateString();

        switch ($level) {
            case MonoLogger::INFO:
                return getcwd() . sprintf('/storage/logs/%s/info.log', $now);
            case MonoLogger::ERROR:
                return getcwd() . sprintf('/storage/logs/%s/error.log', $now);
            default:
                return getcwd() . sprintf('/storage/logs/%s/unknown.log', $now);
        }
    }

    public function log(string $message, int $level = MonoLogger::INFO, array $context = []): void
    {
        // todo: inject MonoLogger instead
        // todo initialize handlers beforehand

        $log = new MonoLogger('name');
        $log->pushHandler(new StreamHandler($this->getFilePathByLevel($level), $level));
        $log->log($level, $message, $context);
    }
}
