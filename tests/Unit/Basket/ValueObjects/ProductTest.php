<?php

declare(strict_types=1);

namespace Tests\Unit\Acme\Basket\ValueObjects;

use Acme\Basket\ValueObjects\Product;
use Money\Money;
use Money\Currency;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        $this->currency = new Currency('USD');
    }

    public function testCanCreateValidProduct(): void
    {
        $product = new Product(
            'REDWIDGET',
            'Red Widget',
            Money::USD(1000) // $10.00
        );

        $this->assertSame('REDWIDGET', $product->code);
        $this->assertSame('Red Widget', $product->name);
        $this->assertEquals(Money::USD(1000), $product->price);
    }

    public function testProductWithZeroPriceIsValid(): void
    {
        $product = new Product(
            'FREEITEM',
            'Free Item',
            Money::USD(0)
        );

        $this->assertEquals(0, $product->price->getAmount());
    }

    public function testThrowsExceptionForNegativePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Price cannot be negative');

        new Product(
            'BADPRICE',
            'Invalid Product',
            Money::USD(-100)
        );
    }

    public function testDifferentCurrenciesAreSupported(): void
    {
        $product = new Product(
            'EUROITEM',
            'Euro Item',
            Money::EUR(1500)
        );

        $this->assertEquals(new Currency('EUR'), $product->price->getCurrency());
    }

    public function testEmptyCodeIsAllowed(): void
    {
        $product = new Product(
            '',
            'Nameless Product',
            Money::USD(200)
        );

        $this->assertSame('', $product->code);
    }

    public function testEmptyNameIsAllowed(): void
    {
        $product = new Product(
            'CODE123',
            '',
            Money::USD(300)
        );

        $this->assertSame('', $product->name);
    }
}
