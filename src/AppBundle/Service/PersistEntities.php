<?php

namespace AppBundle\Service;

use Ddeboer\DataImport\Writer\DoctrineWriter;

/**
 * Class PersistEntities
 *
 * @package AppBundle\Service
 */
class PersistEntities extends DoctrineWriter
{
    /**
     * Flush changes to database
     *
     * @param array $item 
     *
     * @return void
     */
    public function writeItem(array $item)
    {
        $em = $this->entityManager;
        foreach ($item as $product) {
            $em->persist($product);
        }
    }
}
