<?php
declare(strict_types=1);

namespace App\Container;

use App\Console\ScanWebCommand;
use App\Services\Logger\Logger;
use App\Services\Mail\Mailer;
use App\Services\ScanManager\Manager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use FilesystemIterator;

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

        $loader = new FilesystemLoader(getcwd() . '/views/email');
        $twig = new Environment($loader, ['cache' => getcwd() . '/storage/cache']);
        $this->container->set(get_class($twig), $twig);

        $this->registerManagerWebsites();

        $this->container
            ->register(ScanWebCommand::class, ScanWebCommand::class)
            ->addArgument($this->container->get(Manager::getContainerId()))
            ->addArgument($this->container->get(Logger::class))
            ->addArgument($this->container->get(Mailer::class))
            ->addArgument($this->container->get(get_class($twig)))
            ->addArgument($_ENV['NOTIFICATION_EMAIL'])
        ;
    }

    private function registerManagerWebsites(): void
    {
        $manager = $this->container
            ->register(Manager::getContainerId(), Manager::class)
            ->addArgument($_ENV['MANAGER_RESCAN_TIME_SECONDS'] ?? 60)
        ;

        $params = array_filter(explode(',', $_ENV['SCAN_KEYWORDS'] ?? ','));
        $dir = getcwd() . '/app/Services/Websites';

        $iterator = new FilesystemIterator($dir);
        foreach ($iterator as $file) {
            $filename = $file->getFileName();
            if ($file->getExtension() !== 'php') {
                continue;
            }
            if (strpos($filename, 'Abstract') !== false) {
                continue;
            }
            if (strpos($filename, 'Interface') !== false) {
                continue;
            }
            if (strpos($filename, 'Website') === false) {
                continue;
            }

            $fileParts = explode('.', $filename);
            $objPath = 'App\Services\Websites\\' . $fileParts[0];

            $website = $this->container->register($objPath, $objPath);
            foreach ($params as $param) {
                $website->addMethodCall('addKeyword', [$param]);
            }

            $manager->addMethodCall('addWebsite', [$this->container->get($objPath)]);
        }
    }
}
