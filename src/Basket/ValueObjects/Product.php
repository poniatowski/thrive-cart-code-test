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

    public static function create(string $code, string $name, float $price): self
    {
        return new self(
            $code,
            $name,
            Money::USD((int) round($price * 100))
        );
    }
}
