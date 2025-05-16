<?php

declare(strict_types=1);

namespace Tests\Unit\Acme\Basket\Offers;

use Acme\Basket\Offers\NullOfferCalculator;
use Money\Money;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use Acme\Basket\ValueObjects\Product;

class NullOfferCalculatorTest extends TestCase
{
    private NullOfferCalculator $calculator;
    private Currency $currency;

    protected function setUp(): void
    {
        $this->calculator = new NullOfferCalculator();
        $this->currency = new Currency('USD');
    }

    public function testImplementsOfferCalculatorInterface(): void
    {
        $this->assertInstanceOf(
            'Acme\Basket\Contracts\OfferCalculatorInterface',
            $this->calculator
        );
    }

    public function testAlwaysReturnsZeroDiscount(): void
    {
        $items = ['PROD1' => 2, 'PROD2' => 1];
        
        $products = [
            'PROD1' => new Product('PROD1', 'Prod 1', new Money(1000, $this->currency)),
            'PROD2' => new Product('PROD1', 'Prod 1', new Money(500, $this->currency)),
        ];
        
        $result = $this->calculator->calculateDiscount(
            $items,
            $products,
            $this->currency
        );

        $this->assertEquals(0, $result->getAmount());
        $this->assertEquals('USD', $result->getCurrency()->getCode());
    }

    public function testHandlesEmptyBasket(): void
    {
        $result = $this->calculator->calculateDiscount(
            [],
            [],
            $this->currency
        );

        $this->assertEquals(0, $result->getAmount());
    }

    public function testWorksWithDifferentCurrencies(): void
    {
        $eur = new Currency('EUR');
        $result = $this->calculator->calculateDiscount(
            ['PROD1' => 1],
            ['PROD1' => $this->createMockProduct(1000)],
            $eur
        );

        $this->assertEquals(0, $result->getAmount());
        $this->assertEquals('EUR', $result->getCurrency()->getCode());
    }

    public function testIgnoresProductCatalogueContents(): void
    {
        $result1 = $this->calculator->calculateDiscount(
            ['PROD1' => 1],
            ['PROD1' => $this->createMockProduct(1000)], // Valid product
            $this->currency
        );

        $result2 = $this->calculator->calculateDiscount(
            ['PROD1' => 1],
            [], // Empty catalogue
            $this->currency
        );

        $this->assertEquals(0, $result1->getAmount());
        $this->assertEquals(0, $result2->getAmount());
    }

    private function createMockProduct(int $priceInCents): object
    {
        return new class ($priceInCents) {
            public function __construct(private int $price)
            {
            }
            public function price(): Money
            {
                return new Money($this->price, new Currency('USD'));
            }
        };
    }
}
