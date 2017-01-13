<?php

namespace AppBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Logger
 *
 * @package AppBundle\Service
 */
class Logger
{
    /**
     * Log work to Output Interface
     *
     * @param OutputInterface $output       OutputInterface object
     * @param int             $processed    Number of products passed to writer
     * @param array           $csvErrors    Failed to read lines
     * @param array           $insertErrors Failed to insert lines
     *
     * @return void
     */
    public function logWork($output, $processed, $csvErrors, $insertErrors)
    {
        $output->writeln(
            ($processed+count($csvErrors)).
            ' product(s) have been processed'
        );
        $output->writeln(
            '<info>'.
            ($processed-count($insertErrors)).
            ' product(s) have been correctly added</info>'
        );
        $output->writeln(
            '<comment>'.
            (count($insertErrors)+count($csvErrors)).
            ' line(s) have been skipped: </comment>'
        );
        $output->writeln('Failed to read:');
        foreach ($csvErrors as $error) {
            $output->writeln('<comment>'.implode(' ', $error).'</comment>');
        }
        $output->writeln('Failed to insert:');
        foreach ($insertErrors as $error) {
            $output->writeln(
                '<comment>'.
                $error['productCode'].' '.
                $error['productName'].' '.
                $error['productDesc'].' '.
                $error['stock'].' '.
                $error['price'].
                '</comment>'
            );
        }
    }
}
