<?php

declare(strict_types=1);

namespace Tests\Unit\Basket\Offers;

use Acme\Basket\Offers\RedWidgetSecondHalfPriceOffer;
use Acme\Basket\ValueObjects\Product;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RedWidgetSecondHalfPriceOfferTest extends TestCase
{
    private RedWidgetSecondHalfPriceOffer $offerCalculator;
    private array $productCatalogue;

    private Currency $currency;

    protected function setUp(): void
    {
        $this->offerCalculator = new RedWidgetSecondHalfPriceOffer();
        $this->currency = new Currency('USD');

        $this->productCatalogue = [
            'R01' => new Product('R01', 'Red Widget', new Money(3295, $this->currency)), // $32.95
            'G01' => new Product('G01', 'Green Widget', new Money(2495, $this->currency)), // $24.95
            'B01' => new Product('B01', 'Blue Widget', new Money(795, $this->currency)),   // $7.95
        ];
    }

    public function testNoDiscountWhenNoRedWidgets(): void
    {
        $items = ['G01' => 2];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);
        $this->assertTrue($discount->equals(new Money(0, $this->currency)));
    }

    public function testNoDiscountWhenSingleRedWidget(): void
    {
        $items = ['R01' => 1];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);
        $this->assertTrue($discount->equals(new Money(0, $this->currency)));
    }

    public function testDiscountAppliedForTwoRedWidgets(): void
    {
        $items = ['R01' => 2];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);
        $this->assertTrue($discount->equals(new Money(1648, $this->currency))); // $16.48
    }

    public function testDiscountAppliedForThreeRedWidgets(): void
    {
        $items = ['R01' => 3];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);
        $this->assertTrue($discount->equals(new Money(1648, $this->currency))); // Only one pair gets discount
    }

    public function testDiscountAppliedForFourRedWidgets(): void
    {
        $items = ['R01' => 4];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);

        // For 4 red widgets, should get 2 discounts (floor(4/2) = 2)
        // Each discount is $32.95 / 2 = $16.475 → $16.48 when rounded
        // Total discount = 2 × $16.48 = $32.96
        $this->assertTrue($discount->equals(new Money(3296, $this->currency)));
    }

    public function testMixedProductsWithRedWidgets(): void
    {
        $items = ['R01' => 2, 'G01' => 1, 'B01' => 3];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue, $this->currency);
        $this->assertTrue($discount->equals(new Money(1648, $this->currency))); // $16.48
    }
}
