<?php

namespace AppBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class CsvParseLogger
 *
 * @package AppBundle\Service
 */
class CsvParseLogger
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * CsvParseLogger constructor.
     *
     * @param OutputInterface $output OutputInterface object
     */
    public function __construct($output)
    {
        $this->output = $output;
    }


    /**
     * Log work to Output Interface
     *
     * @param int   $processed    Number of products passed to writer
     * @param array $csvErrors    Failed to read lines
     * @param array $insertErrors Failed to insert lines
     *
     * @return void
     */
    public function logWork($processed, $csvErrors, $insertErrors)
    {
        $this->log(
            ($processed+count($csvErrors)).
            ' product(s) have been processed'
        );
        $this->info(
            ($processed-count($insertErrors)).
            ' product(s) have been correctly added'
        );
        $this->comment(
            (count($insertErrors)+count($csvErrors)).
            ' line(s) have been skipped:'
        );
        $this->log('Failed to read:');
        foreach ($csvErrors as $error) {
            $this->comment(implode(' ', $error));
        }
        $this->log('Failed to insert:');
        foreach ($insertErrors as $error) {
            $this->comment(
                $error['productCode'].' '.
                $error['productName'].' '.
                $error['productDesc'].' '.
                $error['stock'].' '.
                $error['price']
            );
        }
    }

    /**
     * Log info(green) line
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->output->writeln('<info>'.$message.'</info>');
    }


    /**
     * Log comment(yellow) line
     *
     * @param $message
     */
    public function comment($message)
    {
        $this->output->writeln('<comment>'.$message.'</comment>');
    }


    /**
     * Log common(white) line
     *
     * @param $message
     */
    public function log($message)
    {
        $this->output->writeln($message);
    }
}
