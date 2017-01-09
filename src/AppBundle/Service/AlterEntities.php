<?php

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Product;

class AlterEntities
{
    protected $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

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

    public function setNewProduct($productData) {
        $product = new Product();
        $product->setStrproductcode($productData['Product Code']);
        $product = $this->setProductData($product, $productData);
        return $product;
    }

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