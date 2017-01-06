<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;
use Symfony\Component\Console\Input\InputArgument;

class CsvParseCommand extends ContainerAwareCommand
{
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new \SplFileObject($input->getArgument('file_path'));
        $reader = new CsvReader($file, ',');
        $reader->setHeaderRowNumber(0);

        $products = $this->productCheck($reader);

        if (!$input->getOption('test')) {
            $this->flushChanges($products['correct']);
        }
        $products['skipping'] = array_merge(
            $reader->getErrors(), $products['skipping']
        );

        $this->logWork($output, $products['correct'], $products['skipping']);
    }

    private function productCheck($reader)
    {
        $products = array('correct' => array(), 'skipping' => array());

        foreach ($reader as $line) {
            $productCost = floatval($line['Cost in GBP']);
            $productKey = $line['Product Code'];
            if ($productCost < 1000) {
                if ($productCost > 5 || intval($line['Stock']) > 10) {
                    $products['correct'][$productKey] = $line;
                }
            } else {
                $products['skipping'][$productKey] = $line;
            }
        }
        return $products;
    }

    private function logWork($output, $correctProducts, $skippingProducts)
    {
        $output->writeln(
            '<info>'.
            count($correctProducts).
            ' product(s) have been correctly added</info>'
        );
        $output->writeln(
            '<comment>'.
            count($skippingProducts).
            ' product(s) have been skippingProducts: </comment>'
        );
        foreach ($skippingProducts as $productData) {
            $output->writeln('<comment>'.implode(' ', $productData).'</comment>');
        }
    }

    private function flushChanges($correctProducts)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $this->getContainer()
            ->get('doctrine')->getRepository('AppBundle:Product');
        foreach ($correctProducts as $productData) {
            $product = $repository->findOneBy(
                array('strProductCode' => $productData['Product Code'])
            );
            if (!$product) {
                $product = $this->setNewProduct($productData);
            } else {
                $product = $this->setProductData($product, $productData);
            }
            $em->merge($product);
        }
        $em->flush();
    }

    private function setNewProduct($productData) {
        $product = new Product();
        $product->setStrproductcode($productData['Product Code']);
        $product = $this->setProductData($product, $productData);
        return $product;
    }

    private function setProductData($product, $productData)
    {
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
