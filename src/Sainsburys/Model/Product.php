<?php

namespace Sainsburys\Model;

use Money\Money;

/**
 * Class Product.
 */
class Product
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var Money
     */
    protected $unitPrice;

    /**
     * Product constructor.
     *
     * @param $title
     * @param $description
     * @param $size
     * @param Money $unitPrice
     */
    public function __construct($title, $description, $size, Money $unitPrice = null)
    {
        $this->title       = $title;
        $this->description = $description;
        $this->size        = $size;
        $this->unitPrice   = $unitPrice;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Money
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return float|string
     */
    public function getPrice()
    {
        if (null == $this->unitPrice) {
            return 'N\A';
        }

        return round($this->unitPrice->getAmount() / 100, 2);
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}
