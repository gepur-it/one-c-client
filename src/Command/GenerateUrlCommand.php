<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.07.18
 */

namespace GepurIt\OneCClientBundle\Command;

use GepurIt\OneCClientBundle\Security\HashGenerator;
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
    /**
     * @var HashGenerator
     */
    private $hashGenerator;

    /**
     * GenerateUrlCommand constructor.
     *
     * @param HashGenerator $hashGenerator
     */
    public function __construct(HashGenerator $hashGenerator)
    {
        $this->hashGenerator = $hashGenerator;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('one-c:generate:url')
            ->setDescription('Generate url fot oneC request')
            ->addArgument('query', InputArgument::REQUIRED, "query to generate url");;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->input = $input;
        $this->output = $output;

        /** @var string $query */
        $query = $this->input->getArgument('query');

        $this->output->writeln($this->hashGenerator->generate($query));

        return 0;
    }
}
