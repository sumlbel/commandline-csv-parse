<?php

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AlterEntities
 *
 * @package AppBundle\Service
 */
class AlterEntities
{
    /**
     * Entity manager
     *
     * @var ObjectManager
     */
    protected $em;

    /**
     * AlterEntities constructor.
     *
     * @param ObjectManager $em ObjectManager object
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Flush changes to database
     *
     * @param array $correctProducts An array of products,
     * that can be correctly added
     *
     * @return void
     */
    public function flushChanges($correctProducts)
    {
        $em = $this->em;
        foreach ($correctProducts as $product) {
            $em->persist($product);
        }
        $em->flush();
    }
}
