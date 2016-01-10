<?php

namespace test\Sainsburys\Scraper;

use Prophecy\Argument;
use Sainsburys\Model\Url;
use Sainsburys\Scraper\ProductListScraper;
use Symfony\Component\DomCrawler\Crawler;

class ProductListScraperTest extends \PHPUnit_Framework_TestCase
{
    protected $productListScraper;

    protected $client;

    public function setUp()
    {
        $this->client             = $this->prophesize('Goutte\Client');
        $this->productListScraper = new ProductListScraper($this->client->reveal());
    }

    public function testEmptyProductListFromEmptyPage()
    {
        $this->client->request('GET', Argument::type('string'))->shouldBeCalled()->willReturn(new Crawler());
        $productLinks = $this->productListScraper->extractProductLinks(new Url('http://empty-page.com'));

        $this->assertTrue(is_array($productLinks));
        $this->assertEmpty($productLinks);
    }

    public function testProductListWithValidLinks()
    {
        $html = <<< 'EOH'
            <div class="product">
                <a href="http://sainsbury.com/products/avocado">avocado</a>
            </div>
            <div class="product">
                <a href="http://sainsbury.com/products/apricot">apricot</a>
            </div>
             <div class="product">
                <a href="http://sainsbury.com/products/avocado-x4">avocado x4</a>
            </div>
EOH;

        $this->client->request('GET', Argument::type('string'))->shouldBeCalled()->willReturn(new Crawler($html));
        $productLinks = $this->productListScraper->extractProductLinks(new Url('http://sainsbury.com/products'));

        $this->assertCount(3, $productLinks);
        $this->assertContainsOnlyInstancesOf('Sainsburys\Model\Url', $productLinks);
    }

    public function testProductListWithInvalidLinks()
    {
        $html = <<< 'EOH'
            <div class="product">
                <a href="foofooffooo">not a valid link</a>
            </div>
            <div class="product">
                <p>Apple</p>
            </div>
            <div class="product">
                <a href="http://sainsbury.com/products/apricot">apricot</a>
            </div>
             <div class="product">
                <a href="http://sainsbury.com/products/avocado-x4">avocado x4</a>
            </div>
EOH;

        $this->client->request('GET', Argument::type('string'))->shouldBeCalled()->willReturn(new Crawler($html));
        $productLinks = $this->productListScraper->extractProductLinks(new Url('http://sainsbury.com/products'));

        $this->assertCount(2, $productLinks);
        $this->assertContainsOnlyInstancesOf('Sainsburys\Model\Url', $productLinks);
    }

    public function testRequestException()
    {
        $requestException = $this->prophesize('GuzzleHttp\Exception\RequestException');
        $this->client->request('GET', 'http://sainsbury.com/products')->shouldBeCalled()->willThrow($requestException->reveal());
        $productLinks = $this->productListScraper->extractProductLinks(new Url('http://sainsbury.com/products'));

        $this->assertTrue(is_array($productLinks));
        $this->assertCount(0, $productLinks);
    }
}
