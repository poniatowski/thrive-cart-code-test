<?php

declare(strict_types=1);

namespace Tests\Unit\Basket;

use Acme\Basket\ShoppingBasket;
use Acme\Basket\Contracts\DeliveryCalculatorInterface;
use Acme\Basket\Contracts\OfferCalculatorInterface;
use Acme\Basket\Delivery\StandardDeliveryCalculator;
use Acme\Basket\Offers\RedWidgetSecondHalfPriceOffer;
use Acme\Basket\ValueObjects\Product;
use Money\Money;
use Money\Currency;
use PHPUnit\Framework\TestCase;

final class ShoppingBasketTest extends TestCase
{
    private array $productCatalogue;
    private DeliveryCalculatorInterface $deliveryCalculator;
    private OfferCalculatorInterface $offerCalculator;
    private ShoppingBasket $basket;
    private Currency $currency;

    protected function setUp(): void
    {
        $this->currency = new Currency('USD');

        $this->productCatalogue = [
            'R01' => new Product('R01', 'Red Widget', Money::USD(3295)), // $32.95
            'G01' => new Product('G01', 'Green Widget', Money::USD(2495)), // $24.95
            'B01' => new Product('B01', 'Blue Widget', Money::USD(795)),    // $7.95
        ];

        $this->deliveryCalculator = new StandardDeliveryCalculator();
        $this->offerCalculator = new RedWidgetSecondHalfPriceOffer();

        $this->basket = new ShoppingBasket(
            $this->productCatalogue,
            $this->deliveryCalculator,
            $this->offerCalculator
        );
    }

    public function testAddProduct(): void
    {
        $this->basket->add('R01');

        // $32.95 (product) + $4.95 (delivery) = $37.90
        $this->assertEquals(37.90, $this->basket->total());
    }

    public function testTwoRedWidgetsApplyDiscount(): void
    {
        $this->basket->add('R01');
        $this->basket->add('R01');

        // ($32.95 + $16.48 discount) = $49.43 + $2.95 delivery = $52.38
        // Note: MoneyPHP handles the precise half calculation
        $this->assertEquals(54.37, $this->basket->total());
    }

    public function testMixedProducts(): void
    {
        $this->basket->add('R01');
        $this->basket->add('G01');

        // $32.95 + $24.95 = $57.90 + $2.95 delivery = $60.85
        $this->assertEquals(60.85, $this->basket->total());
    }

    public function testComplexBasket(): void
    {
        $this->basket->add('B01');
        $this->basket->add('B01');
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->add('R01');

        // Calculation:
        // B01: $7.95 × 2 = $15.90
        // R01: $32.95 × 3 = $98.85
        // Discount: floor(3/2) = 1 × $16.48 = $16.48
        // Subtotal: $15.90 + $98.85 - $16.48 = $98.27
        // Delivery: Free (> $90)
        $this->assertEquals(98.27, $this->basket->total());
    }

    public function testEmptyBasket(): void
    {
        $this->assertEquals(0.00, $this->basket->total());
    }
}
