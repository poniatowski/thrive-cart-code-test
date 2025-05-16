<?php

declare(strict_types=1);

namespace Acme\Basket\Contracts;

use Money\Money;

interface DeliveryCalculatorInterface
{
    public function calculate(Money $amount): Money;
}
