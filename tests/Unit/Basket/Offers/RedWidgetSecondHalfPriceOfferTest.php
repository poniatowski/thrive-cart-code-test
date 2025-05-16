<?php

declare(strict_types=1);

namespace Tests\Unit\Basket\Offers;

use Acme\Basket\Offers\RedWidgetSecondHalfPriceOffer;
use Acme\Basket\ValueObjects\Product;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RedWidgetSecondHalfPriceOfferTest extends TestCase
{
    private RedWidgetSecondHalfPriceOffer $offerCalculator;
    private array $productCatalogue;

    protected function setUp(): void
    {
        $this->offerCalculator = new RedWidgetSecondHalfPriceOffer();

        $this->productCatalogue = [
            'R01' => new Product('R01', 'Red Widget', Money::USD(3295)), // $32.95
            'G01' => new Product('G01', 'Green Widget', Money::USD(2495)), // $24.95
            'B01' => new Product('B01', 'Blue Widget', Money::USD(795)),   // $7.95
        ];
    }

    public function testNoDiscountWhenNoRedWidgets(): void
    {
        $items = ['G01' => 2];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);
        $this->assertTrue($discount->equals(Money::USD(0)));
    }

    public function testNoDiscountWhenSingleRedWidget(): void
    {
        $items = ['R01' => 1];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);
        $this->assertTrue($discount->equals(Money::USD(0)));
    }

    public function testDiscountAppliedForTwoRedWidgets(): void
    {
        $items = ['R01' => 2];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);
        $this->assertTrue($discount->equals(Money::USD(1648))); // $16.48
    }

    public function testDiscountAppliedForThreeRedWidgets(): void
    {
        $items = ['R01' => 3];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);
        $this->assertTrue($discount->equals(Money::USD(1648))); // Only one pair gets discount
    }

    public function testDiscountAppliedForFourRedWidgets(): void
    {
        $items = ['R01' => 4];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);

        // For 4 red widgets, should get 2 discounts (floor(4/2) = 2)
        // Each discount is $32.95 / 2 = $16.475 → $16.48 when rounded
        // Total discount = 2 × $16.48 = $32.96
        $this->assertTrue($discount->equals(Money::USD(3296)));
    }

    public function testMixedProductsWithRedWidgets(): void
    {
        $items = ['R01' => 2, 'G01' => 1, 'B01' => 3];
        $discount = $this->offerCalculator->calculateDiscount($items, $this->productCatalogue);
        $this->assertTrue($discount->equals(Money::USD(1648))); // $16.48
    }
}
