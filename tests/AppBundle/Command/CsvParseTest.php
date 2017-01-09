<?php

namespace Tests\AppBundle\Command;

use AppBundle\Entity\Product;
use AppBundle\Service\AlterEntities;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CsvParseTest extends \PHPUnit_Framework_TestCase
{
    public function testProductCreation()
    {
        $product = $this->createMock(Product::class);

        $employeeRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $employeeRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($product));

        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($employeeRepository));

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

        $this->assertEquals($productData['Product Code'], $product->strProductCode);
        $this->assertEquals($productData['Product Name'], $product->strProductName);
        $this->assertEquals($productData['Product Description'], $product->strProductDesc);
        $this->assertEquals($productData['Cost in GBP'], $product->price);
        $this->assertEquals($productData['Stock'], $product->stock);
        $this->assertNotNull($product->dtmDiscontinued);
        $this->assertNotNull($product->dtmAdded);
        $this->assertNotNull($product->stmTimeStamp);
    }
}
