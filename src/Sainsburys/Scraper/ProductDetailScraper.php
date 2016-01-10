<?php

namespace Sainsburys\Scraper;

use Money\Currency;
use Money\Money;
use Sainsburys\Model\Product;
use Sainsburys\Model\Url;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ProductDetailScraper.
 */
class ProductDetailScraper extends Scraper
{
    const NOT_AVAILABLE        = 'N\A';
    const TITLE_SELECTOR       = 'div.productTitleDescriptionContainer  h1';
    const DESCRIPTION_SELECTOR = 'div.productText:nth-child(2) > p:nth-child(1)';
    const UNIT_PRICE_SELECTOR  = '.pricePerUnit';

    /**
     * @param Url $url
     *
     * @return Product|null
     */
    public function extractDetail(Url $url)
    {
        $crawler = $this->scrape($url);

        if ($crawler) {
            $title       = $this->extractTitle($crawler);
            $description = $this->extractDescription($crawler);
            $unitPrice   = $this->extractUnitPrice($crawler);
            $size        = $this->calculatePageSizeInKb($crawler);

            return new Product($title, $description, $size, $unitPrice);
        }

        return;
    }

    /**
     * @param Crawler $crawler
     *
     * @return int|string
     */
    protected function extractUnitPrice(Crawler $crawler)
    {
        try {
            $unitPriceStr = $crawler->filter(self::UNIT_PRICE_SELECTOR)->text();
            $priceInCents = (int) filter_var($unitPriceStr, FILTER_SANITIZE_NUMBER_FLOAT);
            $unitPrice    = new Money($priceInCents, new Currency('GBP'));
        } catch (\InvalidArgumentException $e) {
            return;
        }

        return $unitPrice;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    protected function extractDescription(Crawler $crawler)
    {
        try {
            return $crawler->filter(self::DESCRIPTION_SELECTOR)->text();
        } catch (\InvalidArgumentException $e) {
            return self::NOT_AVAILABLE;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    protected function extractTitle(Crawler $crawler)
    {
        try {
            return $crawler->filter(self::TITLE_SELECTOR)->text();
        } catch (\InvalidArgumentException $e) {
            return self::NOT_AVAILABLE;
        }
    }

    /**
     * @param string $html
     *
     * @return string
     */
    protected function calculatePageSizeInKb(Crawler $crawler)
    {
        try {
            $html = $crawler->html();
        } catch (\InvalidArgumentException $e) {
            return self::NOT_AVAILABLE;
        }

        $size = mb_strlen($html) / 1024;

        if (is_numeric($size)) {
            $floatSize = (float) $size;

            return round($floatSize, 2).'kb';
        }
    }
}
