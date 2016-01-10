<?php

namespace test\Sainsburys\Model;

use Money\Money;
use Sainsburys\Model\Product;
use Sainsburys\Model\Products;

class ProductsTest extends \PHPUnit_Framework_TestCase
{
    protected function productsProvider()
    {
        $products = new Products();
        $productA = new Product('Avocado', 'Best Avocado in town!', '32.23kb', Money::GBP(180));
        $productB = new Product('Apricot', 'Best Avocado in town!', '32.23kb', Money::GBP(150));
        $productC = new Product('Avocado x4', 'Great Avocado x4 deal', '32.23kb', Money::GBP(280));

        $products->add($productA);
        $products->add($productB);
        $products->add($productC);

        return $products;
    }

    public function testCalculateTotalWithoutProducts()
    {
        $products = new Products();
        $this->assertEquals(0, $products->calculateTotal());
        $this->assertEquals([], $products->toArray());
    }

    public function testCalculateTotalProducts()
    {
        $products = $this->productsProvider();
        $this->assertEquals(6.10, $products->calculateTotal());
    }

    public function testProductsToArray()
    {
        $products     = $this->productsProvider();
        $productsInfo = $products->toArray();

        $this->assertCount(3, $productsInfo['result']);
        $this->assertArrayHasKey('result', $productsInfo);
        $this->assertEquals(6.1, $productsInfo['total']);

        $this->assertEquals('Avocado', $productsInfo['result'][0]['title']);
        $this->assertEquals('32.23kb', $productsInfo['result'][1]['size']);
        $this->assertEquals('Great Avocado x4 deal', $productsInfo['result'][2]['description']);
    }
}
