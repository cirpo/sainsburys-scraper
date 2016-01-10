<?php

namespace Sainsburys\Scraper;

use Sainsburys\Exception\InvalidUrlException;
use Sainsburys\Model\Url;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ProductListScraper.
 */
class ProductListScraper extends Scraper
{
    const PRODUCT_SELECTOR = 'div.product';

    /**
     * It extracts the product links from a given url.
     * If links are found but they're not valid urls, lets just skip.
     *
     * @param Url $url
     *
     * @return array
     */
    public function extractProductLinks(Url $url)
    {
        $urls    = [];
        $crawler = $this->scrape($url);

        if ($crawler) {
            $crawler
                ->filter(self::PRODUCT_SELECTOR)
                ->each(function (Crawler $node) use (&$urls) {
                    $hrefAttr = $node->filter('a')->extract(['href']);

                    if (isset($hrefAttr[0])) {
                        try {
                            $urls[] = new Url($hrefAttr[0]);
                        } catch (InvalidUrlException $e) {
                        }
                    }
                });
        }

        return $urls;
    }
}
