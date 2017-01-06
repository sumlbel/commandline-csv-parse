<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;

class CsvParseCommand extends ContainerAwareCommand
{
	const FILE_PATH = 'app/Resources/stock.csv';
	
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
		$file = new \SplFileObject(self::FILE_PATH);
		$reader = new CsvReader($file, ',');
		$reader->setHeaderRowNumber(0);
		
		$adding = array();
		$skipped = array();
	    $em = $this->getContainer()->get('doctrine')->getManager();
	    
		foreach($reader as $line) {
			if(floatval($line['Cost in GBP']) < 1000) {
				if(floatval($line['Cost in GBP']) > 5 || intval($line['Stock']) > 10) {
					$adding[$line['Product Code']] = $line;
				}
			}
			else {
				$skipped[$line['Product Code']] = $line;
			}
		}
		
		$skipped = array_merge($reader->getErrors(), $skipped);
		if (!$input->getOption('test')) {
			foreach($adding as $productData){
				$product = $this->_setProductData($productData);
	            $em->persist($product);
			}
			$em->flush();
		}
		
		$output->writeln('<info>'.count($adding).' product(s) have been correctly added</info>');
		$output->writeln('<comment>'.count($skipped).' product(s) have been skipped: </comment>');
		foreach($skipped as $productData) {
			$output->writeln('<comment>'.implode(' ', $productData).'</comment>');
		}
	}
	
	private function _setProductData($productData) {
		$product = new Product();
		$product->setStrproductcode($productData['Product Code']);
		$product->setStrproductname($productData['Product Name']);
		$product->setStrproductdesc($productData['Product Description']);
		$product->setStock(intval($productData['Stock']));
		$product->setPrice(floatval($productData['Cost in GBP']));		
		$dateTime = new \DateTime();
		$product->setDtmadded($dateTime);
		if ($productData['Discontinued'] === 'yes') {
			$product->setDtmdiscontinued($dateTime);
		}
		return $product;
	}
}
