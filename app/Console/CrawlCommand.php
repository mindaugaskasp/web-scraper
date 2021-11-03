<?php
declare(strict_types=1);

namespace App\Console;

use App\Services\Logger\Logger;
use App\Services\Mail\Mailer;
use App\Services\CrawlerManager\CrawlerManager;
use App\Services\Websites\Data\Product;
use Carbon\Carbon;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Monolog\Logger as MonoLog;
use Twig\Environment;

class CrawlCommand extends Command
{
    protected static $defaultName = 'app:scan:web';

    private $manager;
    private $logger;
    private $mailer;
    private $twig;
    private $recipient;

    private $iterations = 0;

    public function __construct(CrawlerManager $manager, Logger $logger, Mailer $mailer, Environment $twig, string $emailRecipient)
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
            $output->writeln(sprintf('%s: Crawling. Iteration #%s.', Carbon::now()->toDateTimeString(),  ++$this->iterations));

            $result = [];
            $this->manager->crawl(
                function (UriInterface $url, array $products) use (&$result, $output) {
                    $output->writeln(sprintf('%s: Parsed website %s.', Carbon::now()->toDateTimeString(),  $url->getHost()));
                    $result = array_merge($result, $products);
                }
            );

            !empty($result)
                ? $this->notifyAboutProducts($result)
                : $output->writeln(sprintf('%s: No results found.', Carbon::now()->toDateTimeString()));

            sleep(10);

            return $this->execute($input, $output);
        } catch (Throwable $e) {
            $output->writeln('Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $this->logger->log($e->getMessage(), MonoLog::ERROR);

            return Command::FAILURE;
        }
    }

    private function notifyAboutProducts(array $products): void
    {
        //$this->playSound();

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
