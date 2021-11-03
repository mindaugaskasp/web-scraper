<?php
declare(strict_types=1);

namespace App\Container;

use App\Console\CrawlCommand;
use App\Services\Cache\ProductCache;
use App\Services\Crawler\CrawlerObserver;
use App\Services\Logger\Logger;
use App\Services\Mail\Mailer;
use App\Services\CrawlerManager\CrawlerManager;
use App\Services\Websites\Kaina24Website;
use App\Services\Websites\KilobaitasWebsite;
use App\Services\Websites\SkytechWebsite;
use App\Services\Websites\TopoCentrasWebsite;
use App\Services\Websites\VarleWebsite;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ContainerBindings
{
    private $container;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
    }

    public function getContainer(): TaggedContainerInterface
    {
        return $this->container;
    }

    public function bind(): void
    {
        $this->container->register(Logger::class, Logger::class);
        $this->container->register(Mailer::class, Mailer::class);
        $this->container->register(ProductCache::class, ProductCache::class);
        $this->container
            ->register(CrawlerObserver::class, CrawlerObserver::class)
            ->addArgument($this->container->get(ProductCache::class));


        $loader = new FilesystemLoader(getcwd() . '/views/email');
        $twig = new Environment($loader, ['cache' => getcwd() . '/storage/cache']);
        $this->container->set(get_class($twig), $twig);

        $manager = $this->container
            ->register(CrawlerManager::class, CrawlerManager::class)
            ->addArgument($this->container->get(ProductCache::class))
            ->addArgument($this->container->get(CrawlerObserver::class))
            ->addArgument($this->container->get(Logger::class));

        $keywords = array_filter(explode(',', $_ENV['SCAN_KEYWORDS'] ?? ','));

        $websites[] = $this->container->register(VarleWebsite::class, VarleWebsite::class);
        $websites[] = $this->container->register(KilobaitasWebsite::class, KilobaitasWebsite::class);
        $websites[] = $this->container->register(SkytechWebsite::class, SkytechWebsite::class);
        $websites[] = $this->container->register(TopoCentrasWebsite::class, TopoCentrasWebsite::class);
        $websites[] = $this->container->register(Kaina24Website::class, Kaina24Website::class);


        foreach ($websites as $website) {
            $website->addMethodCall('setKeywords', [$keywords]);
        }

        $manager->addMethodCall('setWebsites', [$websites]);

        $this->container
            ->register(CrawlCommand::class, CrawlCommand::class)
            ->addArgument($this->container->get(CrawlerManager::class))
            ->addArgument($this->container->get(Logger::class))
            ->addArgument($this->container->get(Mailer::class))
            ->addArgument($this->container->get(get_class($twig)))
            ->addArgument($_ENV['NOTIFICATION_EMAIL']);
    }
}
