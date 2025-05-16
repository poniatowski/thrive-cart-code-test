<?php

declare(strict_types=1);

namespace Tests\Unit\Basket\Delivery;

use Acme\Basket\Delivery\StandardDeliveryCalculator;
use Money\Money;
use PHPUnit\Framework\TestCase;

class StandardDeliveryCalculatorTest extends TestCase
{
    private StandardDeliveryCalculator $deliveryCalculator;

    protected function setUp(): void
    {
        $this->deliveryCalculator = new StandardDeliveryCalculator();
    }

    public function testFreeDeliveryForOrdersOver90(): void
    {
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(9000))->equals(Money::USD(0))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(10000))->equals(Money::USD(0))
        );
    }

    public function testReducedDeliveryForOrdersBetween50And90(): void
    {
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(5000))->equals(Money::USD(295))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(7500))->equals(Money::USD(295))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(8999))->equals(Money::USD(295))
        );
    }

    public function testStandardDeliveryForOrdersUnder50(): void
    {
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(0))->equals(Money::USD(495))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(2500))->equals(Money::USD(495))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(4999))->equals(Money::USD(495))
        );
    }

    public function testEdgeCases(): void
    {
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(4999))->equals(Money::USD(495))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(5000))->equals(Money::USD(295))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(8999))->equals(Money::USD(295))
        );
        $this->assertTrue(
            $this->deliveryCalculator->calculate(Money::USD(9000))->equals(Money::USD(0))
        );
    }

    public function testNegativeAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');
        $this->deliveryCalculator->calculate(Money::USD(-1000));
    }
}
