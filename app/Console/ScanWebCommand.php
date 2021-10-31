<?php
declare(strict_types=1);

namespace App\Console;

use App\Services\Logger\Logger;
use App\Services\Mail\Mailer;
use App\Services\ScanManager\Manager;
use App\Services\ScanManager\ScanResultInterface;
use App\Services\Websites\Data\Product;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Monolog\Logger as MonoLog;
use Twig\Environment;

class ScanWebCommand extends Command
{
    protected static $defaultName = 'app:scan:web';

    private $manager;
    private $logger;
    private $mailer;
    private $twig;
    private $recipient;

    private $scanIterations = 0;

    public function __construct(Manager $manager, Logger $logger, Mailer $mailer, Environment $twig, string $emailRecipient)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->recipient = $emailRecipient;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln(sprintf('%s: Scanning. Iteration #%s.', Carbon::now()->toDateTimeString(),  ++$this->scanIterations));

            $result = $this->manager->scan();
            if ($result->getProductCount() !== 0) {
                $output->writeln(sprintf('%s: %s Products found.', Carbon::now()->toDateTimeString(), $result->getProductCount()));
                $this->parseResult($result);
                $this->playSound();
            } else {
                $output->writeln(sprintf('%s: No updates found yet.', Carbon::now()->toDateTimeString()));
            }

            $rescanTime = $this->manager->getRescanTimeSeconds();
            $output->writeln(sprintf('%s: Sleeping for %s seconds.', Carbon::now()->toDateTimeString(), $rescanTime));
            sleep($rescanTime);

            return $this->execute($input, $output);
        } catch (Throwable $e) {
            $output->writeln('Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $this->logger->log($e->getMessage(), MonoLog::ERROR);

            return Command::FAILURE;
        }
    }

    private function parseResult(ScanResultInterface $result): void
    {
        $products = $result->getProducts();
        $html = $this->twig->render('index.html', ['products' => $products]);

        $this->mailer->send(
            'Stock update notification: ' . Carbon::now()->toDayDateTimeString(),
            $html,
            $this->recipient
        );

        /** @var Product $product */
        foreach ($products as $product) {
            $this->logger->log(
                sprintf(
                    '%s: Product found %s (link: %s)',
                    Carbon::now()->toDateTimeString(),
                    $product->getName(),
                    $product->getUrl()
                )
            );
        }
    }

    private function playSound(): void
    {
        $pathToSoundFile = getcwd() . '/storage/notification.mp3';
        $cmd = sprintf('play %s repeat 20 vol 100', $pathToSoundFile);
        exec($cmd . " > /dev/null &");
    }
}
