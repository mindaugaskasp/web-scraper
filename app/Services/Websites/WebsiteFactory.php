<?php
declare(strict_types=1);

namespace App\Services\Websites;

use RuntimeException;

class WebsiteFactory
{
    public function make(string $host): AbstractWebsite
    {
        switch ($host) {
            case SkytechWebsite::getHost():
                return new SkytechWebsite();
            case VarleWebsite::getHost():
                return new VarleWebsite();
            case KilobaitasWebsite::getHost():
                return new KilobaitasWebsite();
            case TopoCentrasWebsite::getHost():
                return new TopoCentrasWebsite();
            case Kaina24Website::getHost():
                return new Kaina24Website();
            default:
                throw new RuntimeException(
                    sprintf('Unknown factory host %s', $host)
                );
        }
    }
}

