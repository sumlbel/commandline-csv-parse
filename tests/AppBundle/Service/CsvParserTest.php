<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/9/17
 * Time: 6:53 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\CsvParser;
use AppBundle\Service\Validator;

class CsvParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test parsing correct csv file
     *
     * @covers CsvParser::parse()
     *
     * @return void
     */
    public function testParsingCorrectCsv()
    {
        $validator = new Validator();
        $reader = $validator->validate('app/Resources/tests/correct.csv');

        $csvParser = new CsvParser();
        $products = $csvParser->parse($reader);
        $product = $products->getCorrect()['P8888'];

        $this->assertEquals($product->getStrProductCode(), 'P8888');
        $this->assertEquals($product->getStrProductName(), 'Speakers');
        $this->assertEquals($product->getStrProductDesc(), '800w');
        $this->assertEquals($product->getPrice(), '99.99');
        $this->assertEquals($product->getStock(), '20');
        $this->assertNotNull($product->getDtmDiscontinued());
        $this->assertEquals($products->getSkipping(), []);
        $this->assertEquals($products->getCountProcessed(), '1');
    }


    /**
     * Test creation of new product
     *
     * @covers CsvParser::setNewProduct()
     *
     * @return void
     */
    public function testProductCreation()
    {
        $csvParser = new CsvParser();

        $productData = ['Product Code' => 'P9999',
            'Product Name' => 'PS4',
            'Product Description' => 'Best Gaming Ever',
            'Cost in GBP' => '120.0',
            'Stock' => '40',
            'Discontinued' => 'yes'];
        $product = $csvParser->setNewProduct($productData);

        $this->assertEquals(
            $productData['Product Code'], $product->getProductCode()
        );
        $this->assertEquals(
            $productData['Product Name'], $product->getProductName()
        );
        $this->assertEquals(
            $productData['Product Description'], $product->getProductDesc()
        );
        $this->assertEquals($productData['Cost in GBP'], $product->getPrice());
        $this->assertEquals($productData['Stock'], $product->getStock());
        $this->assertNotNull($product->getDiscontinued());
        $this->assertNotNull($product->getAdded());
        $this->assertNotNull($product->getTimeStamp());
    }
}
