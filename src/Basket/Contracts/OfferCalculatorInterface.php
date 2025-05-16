<?php

declare(strict_types=1);

namespace Acme\Basket\Contracts;

use Money\Money;

interface OfferCalculatorInterface
{
    /**
     * @param array<string, int> $items
     * @param array<string, \Acme\Basket\ValueObjects\Product> $productCatalogue
     */
    public function calculateDiscount(array $items, array $productCatalogue): Money;
}
