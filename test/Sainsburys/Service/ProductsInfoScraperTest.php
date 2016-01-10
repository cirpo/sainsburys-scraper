<?php

namespace Test\Sainsburys\Scraper;

use Money\Money;
use Sainsburys\Model\Product;
use Sainsburys\Model\Url;
use Sainsburys\Service\ProductsInfoScraper;

class ProductsInfoScraperTest extends \PHPUnit_Framework_TestCase
{
    public function testScrapeProducts()
    {
        $productListUrl     = new Url('http://sainsbury.com/products');
        $productsDetailurls = [
            new Url('http://sainsbury.com/products/avocado'),
            new Url('http://sainsbury.com/products/apricot'),
            new Url('http://sainsbury.com/products/avocado-x4'),
        ];

        $productListScraper   = $this->prophesize('Sainsburys\Scraper\ProductListScraper');
        $productDetailScraper = $this->prophesize('Sainsburys\Scraper\ProductDetailScraper');

        $productListScraper->extractProductLinks($productListUrl)->shouldBeCalled()->willReturn($productsDetailurls);

        $productDetailScraper->extractDetail($productsDetailurls[0])
            ->shouldBeCalled()
            ->willReturn(new Product('Avocado', 'Best Avocado in town!', '32.23kb', Money::GBP(180)));

        $productDetailScraper->extractDetail($productsDetailurls[1])
            ->shouldBeCalled()
            ->willReturn(new Product('Apricot', 'Best Avocado in town!', '32.23kb', Money::GBP(150)));

        $productDetailScraper->extractDetail($productsDetailurls[2])
            ->shouldBeCalled()
            ->willReturn(new Product('Avocado x4', 'Great Avocado x4 deal', '32.23kb', Money::GBP(280)));

        $productsInfoScraper = new ProductsInfoScraper($productListScraper->reveal(), $productDetailScraper->reveal());
        $products            = $productsInfoScraper->extract($productListUrl);

        $this->assertInstanceOf('Sainsburys\Model\Products', $products);
    }
}
