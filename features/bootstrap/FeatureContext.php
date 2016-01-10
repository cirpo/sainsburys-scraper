<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Sainsburys\Command\ProductsScraperCommand;
use Sainsburys\Scraper\ProductDetailScraper;
use Sainsburys\Scraper\ProductListScraper;
use Sainsburys\Service\ProductsInfoScraper;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    protected $commandTester;

    /**
     * @Given the product list page :page exists
     */
    public function theProductListPageExists($page)
    {
        $this->getPageContents($page);
    }

    /**
     * @Given the page has the following product links
     */
    public function thePageHasTheFollowingProductLinks(TableNode $table)
    {
        $productUrls  = $this->getProductListScraper('products.html')->extractProductLinks(new \Sainsburys\Model\Url('http://sainsburys.com/products'));
        $productLinks = [];

        foreach ($productUrls as $productUrl) {
            $productLinks[] = $productUrl->getUrl();
        }

        assertCount(3, $productLinks);

        foreach ($table->getRows() as $row) {
            $productLink = $row[0];
            assertTrue(in_array($productLink, $productLinks));
        }
    }

    /**
     * @When I run the scraper command
     */
    public function iRunTheScraperCommand($page = null)
    {
        if (null == $page) {
            $page = 'products.html';
        }

        $command             = new ProductsScraperCommand($this->getProductsInfoScraper($page));
        $this->commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->commandTester->execute([]);

        return $this->commandTester;
    }

    /**
     * @Then the output should be a Json containing :productCount product's info with :totalPrice as the total price
     */
    public function theOutputShouldBeAJsonContainingProductSInfoWithAsTheTotalPrice($productCount, $totalPrice)
    {
        $result = $this->commandTester->getDisplay();

        assertJson($result);

        $resultToArray = json_decode($result, true);

        assertCount((int) $productCount, $resultToArray['result']);
        assertEquals((float) $totalPrice, $resultToArray['total']);
    }

    /**
     * @Given that the  product page :page doesn't exist
     */
    public function thatTheProductPageDoesnTExist($page)
    {
        try {
            $this->getPageContents($page);
        } catch (Exception $e) {
        }
    }

    /**
     * @When I run the scraper command with :url as invalid url
     */
    public function iRunTheScraperCommandWithAsInvalidUrl($url)
    {
        $this->iRunTheScraperCommand($url);
    }

    /**
     * @Then the output should be :output
     */
    public function theOutputShouldBe($output)
    {
        $result = trim($this->commandTester->getDisplay());
        assertEquals($output, $result);
    }

    protected function getPageContents($page)
    {
        $path = __DIR__.'/../fixtures/'.$page;

        if (!file_exists($path)) {
            throw new Exception(sprintf("The file %s doesn't exists", $path));
        }

        return file_get_contents($path);
    }

    public function getProductListScraper($page)
    {
        try {
            $content = $this->getPageContents($page);
            $mock    = new MockHandler([
                new Response(200, [], $content),
            ]);
        } catch (Exception $e) {
            $mock = new MockHandler([
                new Response(404),
            ]);
        }

        $handler = HandlerStack::create($mock);
        $client  = new Goutte\Client();
        $client->setClient(new Client(['handler' => $handler]));

        return new ProductListScraper($client);
    }

    public function getProductDetailScraper()
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getPageContents('apricot.html')),
            new Response(200, [], $this->getPageContents('avocado.html')),
            new Response(200, [], $this->getPageContents('golden-kiwi.html')),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Goutte\Client();
        $client->setClient(new Client(['handler' => $handler]));

        return new ProductDetailScraper($client);
    }

    public function getProductsInfoScraper($page)
    {
        $productListScraper   = $this->getProductListScraper($page);
        $productDetailScraper = $this->getProductDetailScraper();

        return new ProductsInfoScraper($productListScraper, $productDetailScraper);
    }
}
