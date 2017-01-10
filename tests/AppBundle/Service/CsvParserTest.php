<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/9/17
 * Time: 6:53 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\CsvParser;

class CsvParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test parsing correct csv file
     *
     * @covers CsvParser::parse()
     * @covers CsvParser::splitProducts()
     *
     * @return void
     */
    public function testParsingCorrectCsv()
    {
        $file = new \SplFileObject('app/Resources/test.csv');

        $csvParser = new CsvParser();
        $products = $csvParser->parse($file);
        $productData = $products->getCorrect()['P8888'];

        $this->assertEquals($productData['Product Code'], 'P8888');
        $this->assertEquals($productData['Product Name'], 'Speakers');
        $this->assertEquals($productData['Product Description'], '800w');
        $this->assertEquals($productData['Cost in GBP'], '99.99');
        $this->assertEquals($productData['Stock'], '20');
        $this->assertEquals($productData['Discontinued'], 'yes');
        $this->assertEquals($products->getSkipping(), array());
        $this->assertEquals($products->getCountProcessed(), '1');
    }
}
