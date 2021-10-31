<?php
declare(strict_types=1);

namespace App\Container;

use App\Console\ScanWebCommand;
use App\Services\Logger\Logger;
use App\Services\Mail\Mailer;
use App\Services\ScanManager\Manager;
use App\Services\Websites\Tech\SkytechWebsite;
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
        $this->container->register('log', Logger::class);
        $this->container->register('mailer', Mailer::class);

        $loader = new FilesystemLoader(getcwd() . '/views/email');
        $twig = new Environment($loader, ['cache' => getcwd() . '/storage/cache']);
        $this->container->set('twig', $twig);

        $params = array_filter(explode(',', $_ENV['WEBSITE_SKYTECH_KEYWORDS'] ?? ','));
        $website = $this->container->register(SkytechWebsite::getContainerId(), SkytechWebsite::class);

        foreach ($params as $param) {
            $website->addMethodCall('addKeyword', [$param]);
        }

        $this->container
            ->register(Manager::getContainerId(), Manager::class)
            ->addArgument($_ENV['MANAGER_RESCAN_TIME_SECONDS'] ?? 60)
            ->addMethodCall('addWebsite', [$this->container->get(SkytechWebsite::getContainerId())])
        ;

        $this->container
            ->register('command', ScanWebCommand::class)
            ->addArgument($this->container->get(Manager::getContainerId()))
            ->addArgument($this->container->get('log'))
            ->addArgument($this->container->get('mailer'))
            ->addArgument($this->container->get('twig'))
            ->addArgument($_ENV['NOTIFICATION_EMAIL'])
        ;
    }
}
