<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsvParseCommand extends Command
{

	protected function configure()
	{
		$this
		->setName('demo:parseCSV')
				->addOption(
						'test',
						null,
						InputOption::VALUE_NONE,
						'If set, the task will run in test mode. This will perform 
						everything the normal import does, but not insert the data into the database.'
						);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	
		if ($input->getOption('test')) {
			
		}
	
		$output->writeln();
	}
}