<?php

namespace test\Sainsburys\Scraper;

use Sainsburys\Model\Url;
use Sainsburys\Scraper\ProductDetailScraper;
use Symfony\Component\DomCrawler\Crawler;

class ProductDetailScraperTest extends \PHPUnit_Framework_TestCase
{
    protected $productDetailScraper;

    protected $client;

    public function setUp()
    {
        $this->client               = $this->prophesize('Goutte\Client');
        $this->productDetailScraper = new ProductDetailScraper($this->client->reveal());
    }

    public function testExtractDetail()
    {
        $html = <<< 'EOH'
            <div class="productTitleDescriptionContainer">
                <h1>AVOCADO</h1>
            </div>
            <div class="productText">
                <p>Best avocado in town!</p>
            </div>
             <p class="pricePerUnit">
                £3.50/unit
            </p>
EOH;
        $this->client->request('GET', 'http://sainsbury.com/products/avocado')->shouldBeCalled()->willReturn(new Crawler($html));
        $product = $this->productDetailScraper->extractDetail(new Url('http://sainsbury.com/products/avocado'));

        $this->assertInstanceOf('Sainsburys\Model\Product', $product);
        $this->assertEquals('AVOCADO', $product->getTitle());
        $this->assertEquals('Best avocado in town!', $product->getDescription());
        $this->assertEquals('0.29kb', $product->getSize());
        $this->assertEquals(3.5, $product->getPrice());
    }

    public function testExtractDetailWithNotAvailableData()
    {
        $html = <<< 'EOH'
            <div class="worngProductTitleDescriptionContainerClass">
                <h1>AVOCADO</h1>
            </div>
            <div class="wrongProductTextClass">
                <p>Best avocado in town!</p>
            </div>
             <p class="wrongPricePerUnitClass">
                £3.50/unit
            </p>
EOH;

        $this->client->request('GET', 'http://sainsbury.com/products/avocado')->shouldBeCalled()->willReturn(new Crawler($html));
        $product = $this->productDetailScraper->extractDetail(new Url('http://sainsbury.com/products/avocado'));

        $this->assertInstanceOf('Sainsburys\Model\Product', $product);
        $this->assertEquals('N\A', $product->getTitle());
        $this->assertEquals('N\A', $product->getDescription());
        $this->assertEquals('0.32kb', $product->getSize());
        $this->assertEquals('N\A', $product->getPrice());
    }

    public function testExtractDetailWithNotAvailableHtml()
    {
        $html = '';

        $this->client->request('GET', 'http://sainsbury.com/products/avocado')->shouldBeCalled()->willReturn(new Crawler($html));
        $product = $this->productDetailScraper->extractDetail(new Url('http://sainsbury.com/products/avocado'));

        $this->assertInstanceOf('Sainsburys\Model\Product', $product);
        $this->assertEquals('N\A', $product->getTitle());
        $this->assertEquals('N\A', $product->getDescription());
        $this->assertEquals('N\A', $product->getSize());
        $this->assertEquals('N\A', $product->getPrice());
    }

    public function testRequestException()
    {
        $requestException = $this->prophesize('GuzzleHttp\Exception\RequestException');
        $this->client->request('GET', 'http://sainsbury.com/products/avocado')->shouldBeCalled()->willThrow($requestException->reveal());
        $product = $this->productDetailScraper->extractDetail(new Url('http://sainsbury.com/products/avocado'));

        $this->assertNull($product);
    }
}
