<?php

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Product;

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
        $repository = $this->em->getRepository('AppBundle:Product');
        foreach ($correctProducts as $product) {
            $duplicate = $repository->findOneBy(
                array('strProductCode' => $product->getStrProductCode())
            );
            if ($duplicate) {
                $product = $this->updateProductData($duplicate, $product);
            }
            $em->merge($product);
        }
        $em->flush();
    }

    /**
     * Update product, which already in database
     *
     * @param Product $duplicate Old product funded in repository
     * @param Product $product   New product with same product code
     *
     * @return Product
     */
    public function updateProductData(Product $duplicate, Product $product)
    {
        $duplicate->setStrproductname($product->getStrProductName());
        $duplicate->setStrproductdesc($product->getStrProductDesc());
        $duplicate->setStock($product->getStock());
        $duplicate->setPrice($product->getPrice());
        $duplicate->setDtmdiscontinued($product->getDtmDiscontinued());
        $duplicate->setStmTimeStamp($product->getStmTimeStamp());
        return $duplicate;
    }
}
