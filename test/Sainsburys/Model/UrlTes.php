<?php

namespace test\Sainsburys\Model;

use Sainsburys\Model\Url;

class UrlTes extends \PHPUnit_Framework_TestCase
{
    public function testInitializeValidUrl()
    {
        $url = new Url('http://www.sainsburys.co.uk');
        $this->assertEquals('http://www.sainsburys.co.uk', $url->getUrl());
    }

    /**
     * @expectedException  \Sainsburys\Exception\InvalidUrlException
     */
    public function testInitializeInvalidUrl()
    {
        new Url('');
    }
}
