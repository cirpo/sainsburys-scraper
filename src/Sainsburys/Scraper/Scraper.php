<?php

namespace Sainsburys\Scraper;

use Goutte\Client;
use GuzzleHttp\Exception\RequestException;
use Sainsburys\Model\Url;

class Scraper
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Scraper constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Url $url
     *
     * @return \Symfony\Component\DomCrawler\Crawler|null
     */
    public function scrape(Url $url)
    {
        try {
            return $this->client->request('GET', $url->getUrl());
        } catch (RequestException $e) {
            return;
        }
    }
}
