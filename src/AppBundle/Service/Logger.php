<?php

namespace AppBundle\Service;

use AppBundle\Additional\ParsedProducts;
use Symfony\Component\Console\Output\OutputInterface;


class Logger
{

    /**
     * Logging work to Output Interface
     *
     * @param OutputInterface $output
     * @param ParsedProducts           $products
     *
     * @return void
     */
    public function logWork(OutputInterface $output, ParsedProducts $products)
    {
        $output->writeln(
            '<info>'.
            $products->getCountProcessed().
            ' product(s) have been processed</info>'
        );
        $output->writeln(
            '<info>'.
            count($products->getCorrect()).
            ' product(s) have been correctly added</info>'
        );
        $output->writeln(
            '<comment>'.
            count($products->getSkipping()).
            ' product(s) have been skippingProducts: </comment>'
        );
        foreach ($products->getSkipping() as $productData) {
            $output->writeln('<comment>'.implode(' ', $productData).'</comment>');
        }
    }
}