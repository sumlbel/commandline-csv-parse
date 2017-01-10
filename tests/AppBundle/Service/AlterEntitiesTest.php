<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\AlterEntities;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class AlterEntitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test creation of new product
     *
     * @covers AlterEntities::setNewProduct()
     * @covers AlterEntities::setProductData()
     *
     * @return void
     */
    public function testProductCreation()
    {
        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $alterEntities = new AlterEntities($entityManager);

        $productData = array(
            'Product Code' => 'P9999',
            'Product Name' => 'PS4',
            'Product Description' => 'Best Gaming Ever',
            'Cost in GBP' => '120.0',
            'Stock' => '40',
            'Discontinued' => 'yes',
        );
        $product = $alterEntities->setNewProduct($productData);

        $this->assertEquals(
            $productData['Product Code'], $product->getStrProductCode()
        );
        $this->assertEquals(
            $productData['Product Name'], $product->getStrProductName()
        );
        $this->assertEquals(
            $productData['Product Description'], $product->getStrProductDesc()
        );
        $this->assertEquals($productData['Cost in GBP'], $product->getPrice());
        $this->assertEquals($productData['Stock'], $product->getStock());
        $this->assertNotNull($product->getDtmDiscontinued());
        $this->assertNotNull($product->getDtmAdded());
        $this->assertNotNull($product->getStmTimeStamp());
    }

    /**
     * Test adding product if 'Product Code' already exists
     *
     * @covers AlterEntities::flushChanges()
     *
     * @return void
     */
    public function testAddingProductIfExist()
    {
        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productData = array(
            'Product Code' => 'P9999',
            'Product Name' => 'PS4',
            'Product Description' => 'Best Gaming Ever',
            'Cost in GBP' => '120.0',
            'Stock' => '40',
            'Discontinued' => 'yes',
        );

        $alterEntities = new AlterEntities($entityManager);
        $product = $alterEntities->setNewProduct($productData);

        $employeeRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $employeeRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($product));

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($employeeRepository));

        $correctProducts = array($productData);
        $alterEntities->flushChanges($correctProducts);

        $this->assertEquals(
            $productData['Product Code'], $product->getStrProductCode()
        );
        $this->assertEquals(
            $productData['Product Name'], $product->getStrProductName()
        );
        $this->assertEquals(
            $productData['Product Description'], $product->getStrProductDesc()
        );
        $this->assertEquals($productData['Cost in GBP'], $product->getPrice());
        $this->assertEquals($productData['Stock'], $product->getStock());
        $this->assertNotNull($product->getDtmDiscontinued());
        $this->assertNotNull($product->getDtmAdded());
        $this->assertNotNull($product->getStmTimeStamp());
    }
}

