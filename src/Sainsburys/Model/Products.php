<?php

namespace Sainsburys\Model;

use Money\Money;

/**
 * Class Products is responsible to collect all the products.
 */
class Products
{
    /**
     * @var array
     */
    protected $products = [];

    /**
     * @param Product $product
     */
    public function add(Product $product)
    {
        $this->products[] = $product;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        if (!$this->hasProducts()) {
            return $result;
        }

        foreach ($this->products as $product) {
            $productInfo                = [];
            $productInfo['title']       = $product->getTitle();
            $productInfo['size']        = $product->getSize();
            $productInfo['description'] = $product->getDescription();
            $productInfo['unit_price']  = $product->getPrice();
            $result['result'][]         = $productInfo;
        }

        $result['total'] = $this->calculateTotal();

        return $result;
    }

    /**
     * @return float
     */
    public function calculateTotal()
    {
        /** @var Money $total */
        $total = Money::GBP(0);

        foreach ($this->products as $product) {
            $unitPrice = $product->getUnitPrice() ?: Money::GBP(0);
            $total     = $total->add($unitPrice);
        }

        return round($total->getAmount() / 100, 2);
    }

    /**
     * @return bool
     */
    public function hasProducts()
    {
        return count($this->products) > 0;
    }
}
