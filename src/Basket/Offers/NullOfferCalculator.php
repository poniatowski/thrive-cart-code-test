<?php

declare(strict_types=1);

namespace Acme\Basket\Offers;

use Acme\Basket\Contracts\OfferCalculatorInterface;
use Money\Money;
use Money\Currency;

final class NullOfferCalculator implements OfferCalculatorInterface
{
    public function calculateDiscount(
        array $items,
        array $productCatalogue,
        Currency $currency
    ): Money {
        // Always returns zero discount
        return new Money(0, $currency);
    }
}
