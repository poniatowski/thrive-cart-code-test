<?php

declare(strict_types=1);

namespace Acme\Basket\Delivery;

use Acme\Basket\Contracts\DeliveryCalculatorInterface;
use Money\Money;

class StandardDeliveryCalculator implements DeliveryCalculatorInterface
{
    public function calculate(Money $amount): Money
    {
        if ($amount->isNegative()) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        if ($amount->greaterThanOrEqual(Money::USD(9000))) {
            return Money::USD(0);
        }

        if ($amount->greaterThanOrEqual(Money::USD(5000))) {
            return Money::USD(295);
        }

        return Money::USD(495);
    }
}
