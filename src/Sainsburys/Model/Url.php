<?php

namespace Sainsburys\Model;

use Sainsburys\Exception\InvalidUrlException;

class Url
{
    /**
     * @var string
     */
    protected $url;

    /**
     * Url constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw  new InvalidUrlException();
        }

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
