<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CsvParseCommand
 *
 * @package AppBundle\Command
 */
class CsvParseCommand extends ContainerAwareCommand
{
    /**
     * Configuration of script options and arguments
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('demo:parseCSV')
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                'Perform everything, but not insert the data into the database'
            )
            ->addArgument(
                'file_path',
                InputArgument::REQUIRED,
                'path to correct *.csv file'
            );
    }

    /**
     * Run script, which parsing csv file
     * and making changes to database(not in test mode)
     *
     * @param InputInterface  $input  InputInterface object
     * @param OutputInterface $output OutputInterface object
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = $this->getContainer()->get('app.csv_parser');
        $alter = $this->getContainer()->get('app.alter_entities');
        $logger = $this->getContainer()->get('app.logger');

        $file = new \SplFileObject($input->getArgument('file_path'));
        $products = $parser->parse($file);

        if ($input->getOption('test')) {
            $output->writeln(
                'Running in test mode. No changes will be made in database.'
            );
        } else {
            $alter->flushChanges($products->getCorrect());
        }

        $logger->logWork($output, $products);
    }
}

