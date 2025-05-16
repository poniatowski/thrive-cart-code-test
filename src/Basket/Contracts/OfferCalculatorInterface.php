<?php

declare(strict_types=1);

namespace Acme\Basket\Contracts;

use Money\Currency;
use Money\Money;

interface OfferCalculatorInterface
{
    public function calculateDiscount(array $items, array $productCatalogue, Currency $currency): Money;
}
