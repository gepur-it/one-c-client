<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.07.18
 */

namespace GepurIt\OneCClientBundle\Command;

use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateUrlCommand
 * @package OneCBundle\Command
 */
class GenerateUrlCommand extends Command
{
    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;

    /** @var ApiHttpClient  */
    private $client;

    /**
     * GenerateUrlCommand constructor.
     *
     * @param ApiHttpClient $client
     */
    public function __construct(ApiHttpClient $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('one-c:generate:url')
            ->setDescription('Generate url fot oneC request')
            ->addArgument('query', InputArgument::REQUIRED, "query to generate url");
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $query = $this->input->getArgument('query');

        $this->output->writeln($this->client->generateGetQuery($query));
    }
}
