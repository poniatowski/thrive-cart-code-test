<?php

declare(strict_types=1);

namespace Tests\Integration\Basket;

use Acme\Basket\ShoppingBasket;
use Acme\Basket\Delivery\StandardDeliveryCalculator;
use Acme\Basket\Offers\RedWidgetSecondHalfPriceOffer;
use Acme\Basket\ValueObjects\Product;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ShoppingBasketIntegrationTest extends TestCase
{
    private ShoppingBasket $basket;
    private array $productCatalogue;

    protected function setUp(): void
    {
        $this->productCatalogue = [
            'R01' => new Product('R01', 'Red Widget', Money::USD(3295)), // $32.95
            'G01' => new Product('G01', 'Green Widget', Money::USD(2495)), // $24.95
            'B01' => new Product('B01', 'Blue Widget', Money::USD(795)),   // $7.95
        ];

        $this->basket = new ShoppingBasket(
            $this->productCatalogue,
            new StandardDeliveryCalculator(),
            new RedWidgetSecondHalfPriceOffer()
        );
    }

    public function testEmptyBasket(): void
    {
        $this->assertEquals(0.00, $this->basket->total());
    }

    public function testSingleProductNoDiscount(): void
    {
        $this->basket->add('B01');
        $this->assertEquals(12.90, $this->basket->total()); // $7.95 + $4.95 delivery
    }

    public function testTwoDifferentProducts(): void
    {
        $this->basket->add('B01');
        $this->basket->add('G01');
        $this->assertEquals(37.85, $this->basket->total()); // $7.95 + $24.95 = $32.90 + $4.95 delivery
    }

    public function testRedWidgetOfferApplied(): void
    {
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->assertEquals(54.37, $this->basket->total()); // ($32.95 + $16.48) = $49.43 + $2.95 delivery
    }

    public function testMixedProductsWithOffer(): void
    {
        $this->basket->add('R01');
        $this->basket->add('G01');
        $this->assertEquals(60.85, $this->basket->total()); // $32.95 + $24.95 = $57.90 + $2.95 delivery
    }

    public function testComplexBasketWithMultipleOffers(): void
    {
        $this->basket->add('B01');
        $this->basket->add('B01');
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->add('R01');

        // Calculation:
        // B01: $7.95 × 2 = $15.90
        // R01: $32.95 × 3 = $98.85
        // Discount: floor(3/2) × $16.48 = $16.48
        // Subtotal: $15.90 + $98.85 - $16.48 = $98.27
        // Delivery: Free (> $90)
        $this->assertEquals(98.27, $this->basket->total());
    }

    public function testFreeDeliveryThreshold(): void
    {
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->add('G01');
        $this->basket->add('G01');

        // Calculation:
        // R01: 2 × $32.95 = $65.90
        // Discount: 1 × $16.48 = $16.48
        // G01: 2 × $24.95 = $49.90
        // Subtotal: $65.90 - $16.48 + $49.90 = $99.32
        // Delivery: Free (> $90)
        $this->assertEqualsWithDelta(99.32, $this->basket->total(), 0.01);
    }

    public function testInvalidProductThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->basket->add('X99');
    }

    public function testProductQuantityAccumulation(): void
    {
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->add('R01');

        $reflection = new \ReflectionClass($this->basket);
        $property = $reflection->getProperty('items');
        $property->setAccessible(true);
        $items = $property->getValue($this->basket);

        $this->assertEquals(3, $items['R01']);
    }
}
