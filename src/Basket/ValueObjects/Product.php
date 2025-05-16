<?php

declare(strict_types=1);

namespace Acme\Basket\ValueObjects;

use Money\Money;
use Money\Currency;

final class Product
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly Money $price
    ) {
        if ($price->isNegative()) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
    }
}
