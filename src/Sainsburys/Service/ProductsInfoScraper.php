<?php

namespace Sainsburys\Service;

use Sainsburys\Model\Products;
use Sainsburys\Model\Url;
use Sainsburys\Scraper\ProductDetailScraper;
use Sainsburys\Scraper\ProductListScraper;

/**
 * Class ProductsInfoScraper this class is responsible to get the products info
 * calling the ProductListScraper for getting product links and then calling the
 * ProductDetailScraper to get every product details.
 */
class ProductsInfoScraper
{
    /**
     * ProductsInfoScraper constructor.
     *
     * @param ProductListScraper   $productListScraper
     * @param ProductDetailScraper $productDetailScraper
     */
    public function __construct(ProductListScraper $productListScraper, ProductDetailScraper $productDetailScraper)
    {
        $this->productListScraper   = $productListScraper;
        $this->productDetailScraper = $productDetailScraper;
    }

    /**
     * @param Url $url
     *
     * @return Products
     */
    public function extract(Url $url)
    {
        $products = new Products();

        try {
            $productsUrls = $this->productListScraper->extractProductLinks($url);

            foreach ($productsUrls as $productUrl) {
                $product = $this->productDetailScraper->extractDetail($productUrl);

                if ($product) {
                    $products->add($product);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString();
        }

        return $products;
    }
}
