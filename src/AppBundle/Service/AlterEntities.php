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

    /**
     * Set new product from product data array
     *
     * @param array $productData Array with product data
     *
     * @return Product
     */
    public function setNewProduct($productData)
    {
        $product = new Product();
        $product->setStrproductcode($productData['Product Code']);
        $product = $this->setProductData($product, $productData);
        return $product;
    }

    /**
     * Set data of existing product
     *
     * @param Product $product     Product object
     * @param array   $productData Array with product data
     *
     * @return Product
     */
    public function setProductData(Product $product, $productData)
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
